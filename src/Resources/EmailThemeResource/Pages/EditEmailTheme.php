<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailThemeResource\Pages;

use Filament\Resources\Pages\EditRecord;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailThemeResource;
use Filament\Actions;

class EditEmailTheme extends EditRecord
{
    protected static string $resource = EmailThemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['unlayer_state'])) {
            $data['body_json'] = $data['unlayer_state']['json'] ?? null;
            $data['body_html'] = $data['unlayer_state']['html'] ?? null;
            unset($data['unlayer_state']);
        }

        return $data;
    }
}
