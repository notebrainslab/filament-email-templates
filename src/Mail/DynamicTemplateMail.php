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

    public function __construct(public ?string $templateKey = null, public array $data = [])
    {
        if ($this->templateKey) {
            $this->buildFromTemplate($this->templateKey, $this->data);
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        if ($this->templateKey) {
            return new Envelope(
                subject: $this->resolveTemplateSubject($this->templateKey, $this->data),
            );
        }
        
        return new Envelope();
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if ($this->templateKey) {
            return new Content(
                html: $this->resolveTemplateHtml($this->templateKey, $this->data),
            );
        }

        return new Content();
    }
}
