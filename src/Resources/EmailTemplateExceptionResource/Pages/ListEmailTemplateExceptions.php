<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateExceptionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateExceptionResource;

class ListEmailTemplateExceptions extends ListRecords
{
    protected static string $resource = EmailTemplateExceptionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
