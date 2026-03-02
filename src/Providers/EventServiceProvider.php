<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Providers;

use Illuminate\Support\ServiceProvider;
use NoteBrainsLab\FilamentEmailTemplates\Services\TemplateEventService;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Boot services. Register all event listeners defined in the database.
     */
    public function boot(): void
    {
        // Automatically register all listeners from the database templates
        app(TemplateEventService::class)->registerListeners();
    }
}
