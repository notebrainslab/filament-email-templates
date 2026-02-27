<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTheme;
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
                        Components\TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Components\Toggle::make('is_default')
                            ->label('Default Theme')
                            ->helperText('This theme will be used if no theme is assigned to a template.'),
                    ]),

                Components\Section::make('Layout')
                    ->schema([
                        Components\Textarea::make('header_html')
                            ->label('Header HTML')
                            ->rows(10),
                        Components\Textarea::make('footer_html')
                            ->label('Footer HTML')
                            ->rows(10),
                        Components\Textarea::make('custom_css')
                            ->label('Custom CSS')
                            ->rows(5)
                            ->helperText('Styles added within <style> tags.'),
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
                Tables\Actions\BulkActionGroup::make([
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
