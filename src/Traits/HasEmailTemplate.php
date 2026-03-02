<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Traits;

use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;

trait HasEmailTemplate
{
    /**
     * The unique key identifying which template to use.
     * @var string
     */
    public string $templateKey;

    /**
     * Variables to replace {{placeholders}} in the template.
     * @var array
     */
    public array $templateVariables = [];

    /**
     * Fetch the template by key, replace ALL placeholders, and return
     * the fully merged email ready to send.
     */
    public function build()
    {
        $template = EmailTemplate::where('key', $this->templateKey)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return $this->subject('Template Not Found')
                ->html('<p>Email template configuration is missing for key: <strong>' . e($this->templateKey) . '</strong></p>');
        }

        // 1. Parse placeholders in the Subject line
        $subject = $this->parsePlaceholders($template->subject ?? '');

        // 2. Parse ALL placeholders directly in the Full HTML Design
        // No more ##body_content## wrapper needed.
        $html = $this->parsePlaceholders($template->body_html ?? '');

        if (empty(trim($html))) {
            $html = '<p>No content has been designed for this email template yet.</p>';
        }

        return $this->subject($subject)->html($html);
    }

    /**
     * Replace {{key}} and {{dot.nested}} placeholders using templateVariables.
     * This supports any variable passed from the Mail class.
     */
    protected function parsePlaceholders(string $content): string
    {
        return preg_replace_callback('/\{\{\s*(.*?)\s*\}\}/', function ($matches) {
            $key = trim($matches[1]);
            
            // Replaces {{variable}} with data from $this->templateVariables
            // If the variable is not found, it keeps the placeholder text.
            return data_get($this->templateVariables, $key, $matches[0]);
        }, $content);
    }
}
