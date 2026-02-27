<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Traits;

use Illuminate\Support\Facades\Blade;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Mail\DynamicTemplateMail;
use Illuminate\Support\Facades\App;

trait HasDynamicEmailTemplate
{
    /**
     * Get the email template by key and locale.
     */
    public function getTemplate(string $key, ?string $locale = null): ?EmailTemplate
    {
        $locale = $locale ?? App::getLocale();

        return EmailTemplate::where('key', $key)
            ->where('locale', $locale)
            ->where('is_active', true)
            ->first() ?? EmailTemplate::where('key', $key)
            ->where('locale', 'en')
            ->where('is_active', true)
            ->first();
    }

    /**
     * Parse content with tokens like ##user.name## or ##config.app.name##.
     */
    public function parseTemplateContent(string $content, array $data = []): string
    {
        // Replace button helper tokens: ##button url='https://example.com' title='Click Me'##
        $content = preg_replace_callback('/##button\s+url=\'(.*?)\'\s+title=\'(.*?)\'##/', function ($matches) {
            $url = $matches[1];
            $title = $matches[2];
            return '<a href="' . $url . '" style="background-color: #4F46E5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">' . $title . '</a>';
        }, $content);

        // Replace config tokens: ##config.app.name##
        $content = preg_replace_callback('/##config\.(.*?)##/', function ($matches) {
            return config($matches[1], $matches[0]);
        }, $content);

        // Replace model/data tokens: ##user.name##
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $content = preg_replace_callback('/##' . $key . '\.(.*?)##/', function ($matches) use ($value) {
                    $property = $matches[1];
                    // Support nested properties or methods
                    return data_get($value, $property, $matches[0]);
                }, $content);
            } elseif (is_array($value)) {
                $content = preg_replace_callback('/##' . $key . '\.(.*?)##/', function ($matches) use ($value) {
                    $property = $matches[1];
                    return data_get($value, $property, $matches[0]);
                }, $content);
            } else {
                $content = str_replace('##' . $key . '##', $value, $content);
            }
        }

        return $content;
    }

    /**
     * Initialize the DynamicTemplateMail with parsed content.
     */
    public function buildFromTemplate(string $key, array $data = [], ?string $locale = null): self
    {
        $template = $this->getTemplate($key, $locale);

        if (!$template) {
            return $this;
        }

        $subject = $this->parseTemplateContent($template->subject, $data);
        $bodyHtml = $this->parseTemplateContent($template->body_html, $data);

        // Handle Theme
        $theme = $template->theme ?? \NoteBrainsLab\FilamentEmailTemplates\Models\EmailTheme::where('is_default', true)->first();
        
        if ($theme) {
            $fullHtml = '';
            if ($theme->custom_css) {
                $fullHtml .= '<style>' . $theme->custom_css . '</style>';
            }
            $fullHtml .= $theme->header_html;
            $fullHtml .= $bodyHtml;
            $fullHtml .= $theme->footer_html;
            $bodyHtml = $fullHtml;
        }

        $this->subject($subject);
        $this->html($bodyHtml);

        return $this;
    }
}
