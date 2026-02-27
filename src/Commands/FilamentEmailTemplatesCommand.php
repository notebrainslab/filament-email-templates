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
            '--tag' => 'notebrainslab-filament-email-templates-migrations',
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'notebrainslab-filament-email-templates-config',
        ]);

        if ($this->confirm('Would you like to run the migrations now?')) {
            $this->call('migrate');
        }

        if ($this->confirm('Would you like to seed the default system email templates?')) {
            $this->seedSystemTemplates();
        }

        $this->info('Filament Email Templates installed successfully.');

        return self::SUCCESS;
    }

    protected function seedSystemTemplates(): void
    {
        $templates = [
            [
                'name' => 'Verify Email Address',
                'key' => 'auth.verify_email',
                'subject' => 'Verify your email address',
                'body_html' => '<h1>Hello ##user.name##!</h1><p>Please click the button below to verify your email address.</p><a href="##url##">Verify Email Address</a>',
            ],
            [
                'name' => 'Reset Password',
                'key' => 'auth.reset_password',
                'subject' => 'Reset Password Notification',
                'body_html' => '<h1>Hello ##user.name##!</h1><p>You are receiving this email because we received a password reset request for your account.</p><a href="##url##">Reset Password</a>',
            ],
            [
                'name' => 'New User Registration',
                'key' => 'user.registered',
                'subject' => 'Welcome to ##config.app.name##',
                'body_html' => '<h1>Welcome ##user.name##!</h1><p>Thank you for joining us.</p>',
            ],
        ];

        foreach ($templates as $template) {
            \NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate::updateOrCreate(
                ['key' => $template['key'], 'locale' => 'en'],
                ['name' => $template['name'], 'subject' => $template['subject'], 'body_html' => $template['body_html']]
            );
        }

        $this->info('Default system templates seeded.');
    }
}
