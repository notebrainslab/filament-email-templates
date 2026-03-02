<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource;

class CreateEmailTemplate extends CreateRecord
{
    protected static string $resource = EmailTemplateResource::class;

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
