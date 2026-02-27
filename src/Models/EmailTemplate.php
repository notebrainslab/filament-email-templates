<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    protected $table = 'filament_email_templates';

    protected $guarded = [];

    protected $casts = [
        'recipients' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'attachments' => 'array',
        'is_active' => 'boolean',
        'delay_minutes' => 'integer',
        'body_json' => 'array',
    ];

    public function exceptions(): HasMany
    {
        return $this->hasMany(EmailTemplateException::class, 'email_template_id');
    }
}
