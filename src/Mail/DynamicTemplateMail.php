<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

use NoteBrainsLab\FilamentEmailTemplates\Traits\HasEmailTemplate;

class DynamicTemplateMail extends Mailable
{
    use Queueable, SerializesModels, HasEmailTemplate;

    public function __construct(string $templateKey, array $templateVariables = [])
    {
        $this->templateKey = $templateKey;
        $this->templateVariables = $templateVariables;
    }
}
