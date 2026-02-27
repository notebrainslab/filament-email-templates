<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Mail;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplateException;
use NoteBrainsLab\FilamentEmailTemplates\Mail\DynamicTemplateMail;
use Exception;
use Throwable;
use ReflectionClass;
use ReflectionProperty;

class SendEmailTemplateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $template;
    public $eventPayload;

    /**
     * Create a new job instance.
     *
     * @param EmailTemplate $template
     * @param mixed $eventPayload The event object or data array
     */
    public function __construct(EmailTemplate $template, $eventPayload)
    {
        $this->template = $template;
        $this->eventPayload = $eventPayload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Build context for Blade compilation from public properties of the event
            $context = $this->buildContext($this->eventPayload);

            $recipients = $this->evaluateArray($this->template->recipients, $context);
            $cc = $this->evaluateArray($this->template->cc ?? [], $context);
            $bcc = $this->evaluateArray($this->template->bcc ?? [], $context);
            $attachments = $this->evaluateArray($this->template->attachments ?? [], $context);

            $subject = $this->compileBlade($this->template->subject, $context);
            $bodyHtml = $this->compileBlade($this->template->body_html, $context);

            if (empty($recipients)) {
                return; // Nothing to do if no recipients could be resolved
            }

            $mail = new DynamicTemplateMail($subject, $bodyHtml, $attachments);

            Mail::to($recipients)
                ->cc($cc)
                ->bcc($bcc)
                ->send($mail);

        } catch (Throwable $e) {
            $this->logException($e);
            
            // Re-throw so the job can fail, or we can just swallow it
            // depending on the package's design. Let's fail the job.
            throw $e;
        }
    }

    protected function buildContext($payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (! is_object($payload)) {
            return ['payload' => $payload];
        }

        $context = [];
        $reflection = new ReflectionClass($payload);
        
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();
            $context[$name] = $property->getValue($payload);
        }

        // Always include the object itself, in case they want to do {{ $event->something }} 
        // if they named their event variable in the blade template 'event'.
        $context['event'] = $payload;

        return $context;
    }

    protected function evaluateArray(array $items, array $context): array
    {
        $evaluated = [];
        foreach ($items as $item) {
            $compiled = trim($this->compileBlade($item, $context));
            if (! empty($compiled)) {
                // In case a comma separated list was returned by blade like "a@b.com, c@d.com"
                $parts = array_map('trim', explode(',', $compiled));
                foreach ($parts as $part) {
                    if (!empty($part)) {
                        $evaluated[] = $part;
                    }
                }
            }
        }
        return array_unique($evaluated);
    }

    protected function compileBlade(string $templateString, array $context): string
    {
        if (empty($templateString)) {
            return '';
        }

        // Render blade string using Laravel's Blade facade
        return Blade::render($templateString, $context, deleteCachedView: true);
    }

    protected function logException(Throwable $e): void
    {
        try {
            EmailTemplateException::create([
                'email_template_id' => $this->template->id,
                'event_class' => is_object($this->eventPayload) ? get_class($this->eventPayload) : 'array_or_scalar',
                'payload' => $this->buildContext($this->eventPayload),
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        } catch (Throwable $logEx) {
            // Failsafe so logging exceptions doesn't cause an infinite loop
            \Log::error('Failed to log Email Template Exception: ' . $logEx->getMessage());
        }
    }
}
