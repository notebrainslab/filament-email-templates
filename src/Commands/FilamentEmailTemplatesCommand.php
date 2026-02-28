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
        $theme = \NoteBrainsLab\FilamentEmailTemplates\Models\EmailTheme::firstOrCreate(
            ['is_default' => true],
            [
                'name' => 'Default Theme',
                'body_html' => '<div style="font-family: Arial, sans-serif; padding: 20px;">
                    <header style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                        <h1 style="color: #4F46E5;">##config.app.name##</h1>
                    </header>
                    <main>
                        ##body_content##
                    </main>
                    <footer style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; color: #666; font-size: 12px;">
                        &copy; ' . date('Y') . ' ##config.app.name##. All rights reserved.
                    </footer>
                </div>',
            ]
        );

        $templates = [
            [
                'name' => 'Verify Email Address',
                'key' => 'auth.verify_email',
                'subject' => 'Verify your email address',
                'body' => '<h1>Hello ##user.name##!</h1><p>Please click the button below to verify your email address.</p> ##button url=\'##url##\' title=\'Verify Email Address\'##',
            ],
            [
                'name' => 'Reset Password',
                'key' => 'auth.reset_password',
                'subject' => 'Reset Password Notification',
                'body' => '<h1>Hello ##user.name##!</h1><p>You are receiving this email because we received a password reset request for your account.</p> ##button url=\'##url##\' title=\'Reset Password\'##',
            ],
            [
                'name' => 'New User Registration',
                'key' => 'user.registered',
                'subject' => 'Welcome to ##config.app.name##',
                'body' => '<h1>Welcome ##user.name##!</h1><p>Thank you for joining us.</p>',
            ],
        ];

        foreach ($templates as $template) {
            \NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate::updateOrCreate(
                ['key' => $template['key']],
                [
                    'name' => $template['name'], 
                    'subject' => $template['subject'], 
                    'body' => $template['body'],
                    'theme_id' => $theme->id,
                    'is_active' => true,
                ]
            );
        }

        $this->info('Default theme and system templates seeded.');
    }
}
