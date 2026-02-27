<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $table = 'filament_email_templates';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(EmailTheme::class, 'theme_id');
    }
}
