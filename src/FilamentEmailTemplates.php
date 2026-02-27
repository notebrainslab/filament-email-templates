<?php

namespace NoteBrainsLab\FilamentEmailTemplates;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use NoteBrainsLab\FilamentEmailTemplates\Mail\DynamicTemplateMail;

class FilamentEmailTemplates
{
    /**
     * Register overrides for standard Laravel notifications.
     */
    public function registerDefaultNotifications(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new DynamicTemplateMail('auth.verify_email', [
                'user' => $notifiable,
                'url' => $url,
            ]))->to($notifiable->getEmailForVerification());
        });

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new DynamicTemplateMail('auth.reset_password', [
                'user' => $notifiable,
                'url' => $url,
            ]))->to($notifiable->getEmailForPasswordReset());
        });
    }
}
