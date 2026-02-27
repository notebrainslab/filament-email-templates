<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTheme;
use NoteBrainsLab\FilamentEmailTemplates\Forms\Components\UnlayerEditor;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailThemeResource\Pages;

class EmailThemeResource extends Resource
{
    protected static ?string $model = EmailTheme::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';
    protected static string|\UnitEnum|null $navigationGroup = 'Email Templates';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('General')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Toggle::make('is_default')
                            ->label('Default Theme')
                            ->helperText('This theme will be used if no theme is assigned to a template.'),
                    ])->columns(2),

                Components\Section::make('Design')
                    ->schema([
                        Components\Placeholder::make('design_help')
                            ->content('Design the shell/layout for your emails here. Use ##body_content## as a placeholder for where the template content will be injected.'),
                        
                        UnlayerEditor::make('unlayer_state')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state([
                                        'json' => $record->body_json,
                                        'html' => $record->body_html,
                                    ]);
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->label('Theme Designer (Unlayer)'),
                        
                        Hidden::make('body_html'),
                        Hidden::make('body_json'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->label('Default'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailThemes::route('/'),
            'create' => Pages\CreateEmailTheme::route('/create'),
            'edit' => Pages\EditEmailTheme::route('/{record}/edit'),
        ];
    }
}
