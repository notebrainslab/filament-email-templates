<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

use NoteBrainsLab\FilamentEmailTemplates\Traits\HasDynamicEmailTemplate;

class DynamicTemplateMail extends Mailable
{
    use Queueable, SerializesModels, HasDynamicEmailTemplate;

    public function __construct(string $templateKey, array $data = [], ?string $locale = null)
    {
        $this->buildFromTemplate($templateKey, $data, $locale);
    }

    // Envelope and content are handled by Mailable methods called in the trait,
    // but we can keep them for older override logic if needed.
    // However, the trait already calls $this->subject() and $this->html().
}
