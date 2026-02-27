<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplateException;

class EmailTemplateExceptionResource extends Resource
{
    protected static ?string $model = EmailTemplateException::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'Settings';
    
    public static function getModelLabel(): string
    {
        return __('Email Template Exception');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Email Template Exceptions');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('event_class')
                    ->label('Event Class'),
                Forms\Components\TextInput::make('error_message')
                    ->label('Error Message')
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('payload')
                    ->label('Event Payload')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('trace')
                    ->label('Stack Trace')
                    ->rows(15)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('template.name')
                    ->label('Template')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_class')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('error_message')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplateExceptions::route('/'),
        ];
    }
}
