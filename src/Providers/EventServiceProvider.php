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
            // Ignore all internal Laravel / framework events
            if (
                str_starts_with($eventName, 'illuminate.')      ||  // Illuminate core events
                str_starts_with($eventName, 'eloquent.')        ||  // Eloquent model events
                str_starts_with($eventName, 'bootstrapped:')    ||  // App bootstrap events
                str_starts_with($eventName, 'bootstrapping:')   ||  // App bootstrapping events
                str_starts_with($eventName, 'Illuminate\\')     ||  // Class-based Illuminate events
                str_starts_with($eventName, 'Laravel\\')        ||  // Class-based Laravel events
                str_starts_with($eventName, 'creating:')        ||  // Eloquent creating
                str_starts_with($eventName, 'created:')         ||  // Eloquent created
                str_starts_with($eventName, 'updating:')        ||  // Eloquent updating
                str_starts_with($eventName, 'updated:')         ||  // Eloquent updated
                str_starts_with($eventName, 'saving:')          ||  // Eloquent saving
                str_starts_with($eventName, 'saved:')           ||  // Eloquent saved
                str_starts_with($eventName, 'deleting:')        ||  // Eloquent deleting
                str_starts_with($eventName, 'deleted:')         ||  // Eloquent deleted
                str_starts_with($eventName, 'restoring:')       ||  // Eloquent restoring
                str_starts_with($eventName, 'restored:')           // Eloquent restored
            ) {
                return;
            }

            app(TemplateRenderer::class)->handleEvent($eventName, $data);
        });
    }
}
