<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class DynamicTemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $bodyHtml;
    public array $emailAttachments;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $bodyHtml
     * @param array $attachments
     */
    public function __construct(string $subject, string $bodyHtml, array $attachments = [])
    {
        $this->emailSubject = $subject;
        $this->bodyHtml = $bodyHtml;
        $this->emailAttachments = $attachments;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->bodyHtml,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $mailAttachments = [];
        
        foreach ($this->emailAttachments as $attachmentPath) {
            if (is_string($attachmentPath) && file_exists($attachmentPath)) {
                $mailAttachments[] = Attachment::fromPath($attachmentPath);
            }
        }

        return $mailAttachments;
    }
}
