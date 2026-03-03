<?php

namespace NoteBrainsLab\FilamentEmailTemplates;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use NoteBrainsLab\FilamentEmailTemplates\Commands\FilamentEmailTemplatesCommand;

class FilamentEmailTemplatesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('notebrainslab-filament-email-templates')
            ->hasConfigFile('filament-email-templates')
            ->hasViews('filament-email-templates')
            ->hasMigration('create_filament_email_templates_table')
            ->hasCommand(FilamentEmailTemplatesCommand::class);
    }
}
