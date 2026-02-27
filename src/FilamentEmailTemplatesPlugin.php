<?php

namespace NoteBrainsLab\FilamentEmailTemplates;

use Filament\Contracts\Plugin;
use Filament\Panel;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource;


class FilamentEmailTemplatesPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-email-templates';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            EmailTemplateResource::class,
            Resources\EmailThemeResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        if (config('filament-email-templates.register_notifications')) {
            app(\NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplates::class)->registerDefaultNotifications();
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
