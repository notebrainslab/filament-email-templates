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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $data['unlayer_state'] = json_encode([
            'design' => $record->body_json ?? null,
            'html'   => $record->body_html ?? null,
        ]);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
