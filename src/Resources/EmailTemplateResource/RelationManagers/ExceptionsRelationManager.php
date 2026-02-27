<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Infolists\Components as InfolistComponents;
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
                InfolistComponents\Section::make('Exception Details')
                    ->schema([
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
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
