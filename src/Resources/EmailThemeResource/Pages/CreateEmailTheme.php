<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailThemeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailThemeResource;

class CreateEmailTheme extends CreateRecord
{
    protected static string $resource = EmailThemeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['unlayer_state'])) {
            $data['body_json'] = $data['unlayer_state']['json'] ?? null;
            $data['body_html'] = $data['unlayer_state']['html'] ?? null;
            unset($data['unlayer_state']);
        }

        return $data;
    }
}
