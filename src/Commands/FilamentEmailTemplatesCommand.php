<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Commands;

use Illuminate\Console\Command;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;

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
        // All default Laravel auth event templates — each has its own full HTML design
        $templates = [
            [
                'name'    => 'Verify Email Address',
                'key'     => 'auth.verify_email',
                'event'   => \Illuminate\Auth\Events\Verified::class,
                'subject' => 'Verify Your Email Address',
                'recipients' => ['user'],
                'body_html' => $this->defaultHtml(
                    'Verify Your Email Address',
                    '<p style="font-size:15px;color:#374151;">Hello <strong>{{user.name}}</strong>,</p>
                     <p style="font-size:15px;color:#374151;">Please click the button below to verify your email address.</p>
                     <p style="text-align:center;margin:30px 0;">
                       <a href="{{url}}" style="background:#4F46E5;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:15px;font-weight:600;">Verify Email Address</a>
                     </p>
                     <p style="font-size:13px;color:#6B7280;">If you did not create an account, no further action is required.</p>'
                ),
            ],
            [
                'name'    => 'Reset Password',
                'key'     => 'auth.reset_password',
                'event'   => \Illuminate\Auth\Events\PasswordReset::class,
                'subject' => 'Reset Your Password',
                'recipients' => ['user'],
                'body_html' => $this->defaultHtml(
                    'Reset Your Password',
                    '<p style="font-size:15px;color:#374151;">Hello <strong>{{user.name}}</strong>,</p>
                     <p style="font-size:15px;color:#374151;">You are receiving this email because we received a password reset request for your account.</p>
                     <p style="text-align:center;margin:30px 0;">
                       <a href="{{url}}" style="background:#4F46E5;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:15px;font-weight:600;">Reset Password</a>
                     </p>
                     <p style="font-size:13px;color:#6B7280;">This password reset link will expire in 60 minutes.</p>
                     <p style="font-size:13px;color:#6B7280;">If you did not request a password reset, no further action is required.</p>'
                ),
            ],
            [
                'name'    => 'Welcome — User Registered',
                'key'     => 'user.registered',
                'event'   => \Illuminate\Auth\Events\Registered::class,
                'subject' => 'Welcome to {{app_name}}!',
                'recipients' => ['user'],
                'body_html' => $this->defaultHtml(
                    'Welcome, {{user.name}}!',
                    '<p style="font-size:15px;color:#374151;">Thank you for registering with us. Your account is now active and ready to use.</p>
                     <p style="font-size:15px;color:#374151;">You can log in anytime using the button below:</p>
                     <p style="text-align:center;margin:30px 0;">
                       <a href="{{login_url}}" style="background:#4F46E5;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-size:15px;font-weight:600;">Go to Dashboard</a>
                     </p>'
                ),
            ],
            [
                'name'    => 'Login Notification',
                'key'     => 'user.login',
                'event'   => \Illuminate\Auth\Events\Login::class,
                'subject' => 'New Login to Your Account',
                'recipients' => ['user'],
                'body_html' => $this->defaultHtml(
                    'New Login Detected',
                    '<p style="font-size:15px;color:#374151;">Hello <strong>{{user.name}}</strong>,</p>
                     <p style="font-size:15px;color:#374151;">We detected a new login to your account.</p>
                     <p style="font-size:13px;color:#6B7280;">If this was you, you can safely ignore this email. If you did not log in, please secure your account immediately.</p>'
                ),
            ],
            [
                'name'    => 'Account Locked Out',
                'key'     => 'user.lockout',
                'event'   => \Illuminate\Auth\Events\Lockout::class,
                'subject' => 'Account Locked Out — Too Many Attempts',
                'recipients' => ['user'],
                'body_html' => $this->defaultHtml(
                    'Account Temporarily Locked',
                    '<p style="font-size:15px;color:#374151;">Hello,</p>
                     <p style="font-size:15px;color:#374151;">Your account has been temporarily locked due to too many failed login attempts. Please try again later.</p>
                     <p style="font-size:13px;color:#6B7280;">If you did not attempt to log in, please contact support immediately.</p>'
                ),
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['key' => $template['key']],
                [
                    'name'        => $template['name'],
                    'event'       => $template['event'],
                    'subject'     => $template['subject'],
                    'recipients'  => $template['recipients'],
                    'attachments' => [],
                    'delay_minutes' => 0,
                    'body_html'   => $template['body_html'],
                    'body_json'   => null, // no Unlayer JSON for seeded templates
                    'is_active'   => true,
                ]
            );

            $this->line("  ✓ Seeded: <info>{$template['name']}</info>");
        }

        $this->info('Default system email templates seeded successfully.');
    }

    /**
     * Generate a clean, minimal default email HTML wrapper.
     */
    protected function defaultHtml(string $title, string $bodyContent): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#F9FAFB;font-family:Arial,Helvetica,sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F9FAFB;">
    <tr>
      <td align="center" style="padding:40px 20px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background-color:#ffffff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.1);">

          <!-- Header -->
          <tr>
            <td style="padding:32px 40px 24px;border-bottom:1px solid #E5E7EB;">
              <h2 style="margin:0;font-size:20px;font-weight:700;color:#111827;">{$title}</h2>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:32px 40px;">
              {$bodyContent}
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding:24px 40px;border-top:1px solid #E5E7EB;text-align:center;">
              <p style="margin:0;font-size:12px;color:#9CA3AF;">&copy; {{app_name}}. All rights reserved.</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }
}
