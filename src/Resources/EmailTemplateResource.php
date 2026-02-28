<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource\Pages;
class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;
    
    public static function getNavigationGroup(): ?string
    {
        return \NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return \NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesPlugin::get()->getNavigationIcon() ?? 'heroicon-o-envelope-open';
    }

    public static function getNavigationSort(): ?int
    {
        return \NoteBrainsLab\FilamentEmailTemplates\FilamentEmailTemplatesPlugin::get()->getNavigationSort();
    }
    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('Email Template');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Email Templates');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Tabs::make('Tabs')
                    ->tabs([
                        Components\Tabs\Tab::make('General')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                
                                TextInput::make('key')
                                    ->required()
                                    ->helperText('Unique key for this template, used to reference from Trait.')
                                    ->maxLength(255),



                                TextInput::make('subject')
                                    ->required()
                                    ->helperText('Supports tokens like {{user_name}} or {{order_id}}')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                
                                Toggle::make('is_active')
                                    ->default(true),

                                Select::make('theme_id')
                                    ->relationship('theme', 'name')
                                    ->required()
                                    ->live()
                                    ->afterStateHydrated(fn ($state, $set) => $set('theme_html', \NoteBrainsLab\FilamentEmailTemplates\Models\EmailTheme::find($state)?->body_html))
                                    ->afterStateUpdated(fn ($state, $set) => $set('theme_html', \NoteBrainsLab\FilamentEmailTemplates\Models\EmailTheme::find($state)?->body_html))
                                    ->helperText('Select a design theme for this template.'),
                                
                                Hidden::make('theme_html'),
                            ])->columns(2),

                        Components\Tabs\Tab::make('Content')
                            ->schema([
                                Placeholder::make('token_help')
                                    ->label('Available Tokens')
                                    ->content('Use {{user_name}}, {{order_id}}, etc. in your content.'),

                                RichEditor::make('body')
                                    ->label('Email Content Body')
                                    ->live()
                                    ->columnSpanFull(),
                            ]),
                        
                        Components\Tabs\Tab::make('Preview')
                            ->schema([
                                ViewField::make('preview')
                                    ->view('filament-email-templates::forms.components.preview')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->limit(50),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
