<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Services;

use Illuminate\Support\Facades\Event;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Mail\DynamicTemplateMail;
use Illuminate\Support\Facades\Mail;

class TemplateEventService
{
    /**
     * Register listeners for all active templates mapped to a Laravel Event.
     */
    public function registerListeners(): void
    {
        $templates = EmailTemplate::where('is_active', true)
            ->whereNotNull('event')
            ->get();

        foreach ($templates as $template) {
            Event::listen($template->event, function ($eventInstance) use ($template) {
                $this->handleEvent($template, $eventInstance);
            });
        }
    }

    /**
     * Handle the event firing and send the email using all event-provided data.
     */
    protected function handleEvent(EmailTemplate $template, $event): void
    {
        // 1. Prepare variables from the event object
        // We capture any object or variable passed by the event and make it available.
        $variables = [
            'event' => $event,
            'app_name' => config('app.name'),
        ];

        // Capture standard Laravel data if present
        if (isset($event->user)) {
            $variables['user'] = $event->user;
        } elseif (isset($event->notifiable)) {
            $variables['user'] = $event->notifiable;
        }

        // 2. Resolve Recipients
        $to = [];
        if (in_array('user', $template->recipients ?? []) && isset($variables['user'])) {
            $to[] = $variables['user']->email;
        }

        // If no recipient is found, we don't send the email.
        if (empty($to)) return;

        // 3. Create the Mail instance
        $mail = new DynamicTemplateMail($template->key, $variables);

        // 4. Handle Delay or Send Immediately
        if ($template->delay_minutes > 0) {
            Mail::to($to)->later(now()->addMinutes($template->delay_minutes), $mail);
        } else {
            Mail::to($to)->send($mail);
        }
    }
}
