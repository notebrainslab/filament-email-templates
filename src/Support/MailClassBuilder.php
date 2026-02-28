<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;

class MailClassBuilder
{
    public static function build(EmailTemplate $template): bool
    {
        $className = Str::studly($template->key);
        if (!Str::endsWith($className, 'Mail')) {
            $className .= 'Mail';
        }

        $directory = app_path('Mail/VisualBuilder/EmailTemplates');
        $filePath = "$directory/$className.php";

        if (File::exists($filePath)) {
            return false;
        }

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $stub = self::getStub();
        $content = str_replace(
            ['{{ className }}', '{{ templateKey }}'],
            [$className, $template->key],
            $stub
        );

        File::put($filePath, $content);
        
        $template->update(['mail_class' => $className]);

        return true;
    }

    protected static function getStub(): string
    {
        return <<<PHP
<?php

namespace App\Mail\VisualBuilder\EmailTemplates;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use NoteBrainsLab\FilamentEmailTemplates\Mail\DynamicTemplateMail;

class {{ className }} extends DynamicTemplateMail
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param array \$data Tokens/Variables to replace in the template
     */
    public function __construct(public array \$data = [])
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: \$this->resolveTemplateSubject('{{ templateKey }}', \$this->data),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: \$this->resolveTemplateHtml('{{ templateKey }}', \$this->data),
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
PHP;
    }
}
