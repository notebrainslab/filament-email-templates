<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailThemeResource\Pages;

use Filament\Resources\Pages\ListRecords;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailThemeResource;
use Filament\Actions;

class ListEmailThemes extends ListRecords
{
    protected static string $resource = EmailThemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
