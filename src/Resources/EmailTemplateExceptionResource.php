<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Infolists\Components as InfolistComponents;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplateException;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateExceptionResource\Pages;

class EmailTemplateExceptionResource extends Resource
{
    protected static ?string $model = EmailTemplateException::class;
    
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static string|\UnitEnum|null $navigationGroup = 'Email Templates';
    protected static ?int $navigationSort = 2;
    
    public static function getModelLabel(): string
    {
        return __('Email Template Exception');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Email Template Exceptions');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'danger' : 'gray';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfolistComponents\Section::make('Exception Details')
                    ->schema([
                        InfolistComponents\TextEntry::make('template.key')
                            ->label('Template Key'),
                        InfolistComponents\TextEntry::make('template.locale')
                            ->label('Locale'),
                        InfolistComponents\TextEntry::make('error_message')
                            ->label('Error Message')
                            ->columnSpanFull()
                            ->color('danger')
                            ->weight('bold'),
                        InfolistComponents\KeyValueEntry::make('payload')
                            ->label('Context Data')
                            ->columnSpanFull(),
                        InfolistComponents\TextEntry::make('trace')
                            ->label('Stack Trace')
                            ->formatStateUsing(fn ($state) => $state)
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'whitespace-pre-wrap font-mono text-xs']),
                    ])->columns(2),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Logic moved to infolist for viewing
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
                Tables\Columns\TextColumn::make('template.key')
                    ->label('Key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('template.locale')
                    ->label('Locale')
                    ->badge(),
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
                Actions\ViewAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
