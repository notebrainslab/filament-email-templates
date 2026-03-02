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
            ->hasTranslations()
            ->hasMigration('create_filament_email_templates_table')
            ->hasMigration('create_filament_email_themes_table')      // kept for existing installs
            ->hasMigration('restructure_filament_email_templates_table') // new unified schema
            ->hasCommand(FilamentEmailTemplatesCommand::class);
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();
        
        $this->app->register(\NoteBrainsLab\FilamentEmailTemplates\Providers\EventServiceProvider::class);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        // Register any custom package booted logic here
    }
}
