<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Commands;

use Illuminate\Console\Command;

class FilamentEmailTemplatesCommand extends Command
{
    public $signature = 'filament-email-templates:install';

    public $description = 'Install the Filament Email Templates plugin';

    public function handle(): int
    {
        $this->comment('Installing Filament Email Templates plugin...');

        $this->call('vendor:publish', [
            '--tag' => 'filament-email-templates-migrations',
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'filament-email-templates-config',
        ]);

        if ($this->confirm('Would you like to run the migrations now?')) {
            $this->call('migrate');
        }

        $this->info('Filament Email Templates installed successfully.');

        return self::SUCCESS;
    }
}
