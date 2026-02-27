<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

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
