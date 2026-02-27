<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class ExceptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'exceptions';

    protected static ?string $recordTitleAttribute = 'error_message';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Read-only in relation manager too
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaComponents\Section::make('Exception Details')
                    ->schema([
                        SchemaComponents\TextEntry::make('error_message')
                            ->label('Error Message')
                            ->columnSpanFull()
                            ->color('danger')
                            ->weight('bold'),
                        SchemaComponents\KeyValueEntry::make('payload')
                            ->label('Context Data')
                            ->columnSpanFull(),
                        SchemaComponents\TextEntry::make('trace')
                            ->label('Stack Trace')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'whitespace-pre-wrap font-mono text-xs']),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
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
            ->headerActions([
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
}
