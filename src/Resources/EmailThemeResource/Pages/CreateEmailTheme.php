<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailThemeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailThemeResource;

class CreateEmailTheme extends CreateRecord
{
    protected static string $resource = EmailThemeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['unlayer_state']) && is_string($data['unlayer_state'])) {
            $parsed = json_decode($data['unlayer_state'], true);
            $data['body_json'] = $parsed['design'] ?? null;
            $data['body_html'] = $parsed['html'] ?? null;
        }

        unset($data['unlayer_state']);

        return $data;
    }
}
