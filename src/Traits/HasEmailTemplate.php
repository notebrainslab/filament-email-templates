<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Traits;

use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTheme;

trait HasEmailTemplate
{
    /**
     * @var string
     */
    public string $templateKey;

    /**
     * @var array
     */
    public array $templateVariables = [];

    /**
     * Fetch and build the template.
     */
    public function build()
    {
        $template = EmailTemplate::where('key', $this->templateKey)->where('is_active', true)->first();

        if (!$template) {
            return $this->subject('Template Not Found')->html('Template configuration is missing.');
        }

        $subject = $this->parsePlaceholders($template->subject ?? '');
        $bodyContent = $this->parsePlaceholders($template->body ?? '');
        
        $theme = $template->theme ?? EmailTheme::where('is_default', true)->first();

        // Handle Theme/Design
        if ($theme && $theme->body_html) {
            $html = str_replace(['##body_content##', '{{body_content}}'], $bodyContent, $theme->body_html);
        } else {
            $html = $bodyContent;
        }

        return $this->subject($subject)->html($html);
    }

    /**
     * Parse placeholders like {{user_name}}.
     */
    protected function parsePlaceholders(string $content): string
    {
        return preg_replace_callback('/\{\{\s*(.*?)\s*\}\}/', function ($matches) {
            $key = trim($matches[1]);
            return data_get($this->templateVariables, $key, $matches[0]);
        }, $content);
    }
}
