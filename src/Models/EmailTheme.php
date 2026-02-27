<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTheme extends Model
{
    protected $table = 'filament_email_themes';

    protected $guarded = [];

    protected $casts = [
        'is_default' => 'boolean',
        'body_json' => 'array',
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(EmailTemplate::class, 'theme_id');
    }
}
