<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource;
use NoteBrainsLab\FilamentEmailTemplates\Support\MailClassBuilder;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('build_class')
                ->label('Build Class')
                ->icon('heroicon-m-code-bracket')
                ->color('success')
                ->action(function () {
                    $record = $this->getRecord();
                    $success = MailClassBuilder::build($record);
                    
                    if ($success) {
                        Notification::make()
                            ->title('Mail Class Created')
                            ->body("Class generated successfully in App\Mail\VisualBuilder\EmailTemplates")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Class Already Exists')
                            ->warning()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }


}
