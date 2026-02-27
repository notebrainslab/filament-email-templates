<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Services;

use Exception;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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
            // Guard: skip if migrations haven't been run yet
            if (! Schema::hasTable((new EmailTemplate())->getTable())) {
                return;
            }

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
            // IMPORTANT: Do NOT use Log::error() here — it fires Illuminate\Log\Events\MessageLogged
            // which would re-trigger this wildcard listener, causing an infinite loop.
            // Use native PHP error_log() instead which does not dispatch Laravel events.
            error_log('FilamentEmailTemplates: Event dispatch error for [' . $eventName . ']: ' . $e->getMessage());
        }
    }
}
