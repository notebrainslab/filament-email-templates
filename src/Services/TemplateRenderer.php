<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Jobs\SendEmailTemplateJob;

class TemplateRenderer
{
    /**
     * Handle incoming events from the EventServiceProvider.
     */
    public function handleEvent(string $eventName, array $data): void
    {
        try {
            // Find all active templates matching this event class
            $templates = EmailTemplate::where('event_class', $eventName)
                ->where('is_active', true)
                ->get();

            if ($templates->isEmpty()) {
                return;
            }

            // The actual event instance is generally the first item in the data array
            $eventInstance = $data[0] ?? null;

            if (! is_object($eventInstance)) {
                // If the event doesn't seem to be an object class event, fallback to passing array
                $eventInstance = $data;
            }

            foreach ($templates as $template) {
                // Dispatch job for each matching template
                $job = new SendEmailTemplateJob($template, clone $eventInstance);

                if ($template->delay_minutes > 0) {
                    $job->delay(now()->addMinutes($template->delay_minutes));
                }

                dispatch($job);
            }
        } catch (Exception $e) {
            // Capture any exceptions during dispatch so it doesn't break the application
            Log::error('Event Template Dispatch Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }
}
