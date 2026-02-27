<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use NoteBrainsLab\FilamentEmailTemplates\Services\TemplateRenderer;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen('*', function ($eventName, array $data) {
            // Ignore wildcard sub-events or framework events that probably aren't meant to trigger emails
            if (str_starts_with($eventName, 'illuminate.') || str_starts_with($eventName, 'eloquent.')) {
                return;
            }

            // A singleton or service class should handle the template check and sending
            app(TemplateRenderer::class)->handleEvent($eventName, $data);
        });
    }
}
