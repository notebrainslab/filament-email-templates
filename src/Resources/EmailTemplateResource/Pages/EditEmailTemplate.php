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

    /**
     * When loading the edit form, pack body_json + body_html into unlayer_state
     * so the Unlayer editor can hydrate itself.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $data['unlayer_state'] = json_encode([
            'design' => $record->body_json ?? null,
            'html'   => $record->body_html ?? null,
        ]);

        return $data;
    }

    /**
     * Before saving, unpack unlayer_state back into body_json and body_html.
     */
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
