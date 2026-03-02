<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'filament_email_templates';

    protected $guarded = [];

    protected $casts = [
        'is_active'   => 'boolean',
        'recipients'  => 'array',
        'attachments' => 'array',
        'body_json'   => 'array',
    ];
}
