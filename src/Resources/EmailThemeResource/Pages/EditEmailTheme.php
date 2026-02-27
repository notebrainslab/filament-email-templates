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
}
