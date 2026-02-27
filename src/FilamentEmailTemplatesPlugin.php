<?php

namespace NoteBrainsLab\FilamentEmailTemplates;

use Filament\Contracts\Plugin;
use Filament\Panel;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateExceptionResource;

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
            EmailTemplateExceptionResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Setup any dependencies
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
