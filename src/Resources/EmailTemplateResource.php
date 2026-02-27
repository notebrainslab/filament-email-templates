<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Forms\Components\UnlayerEditor;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource\Pages;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource\RelationManagers;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;
    
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope-open';
    protected static string|\UnitEnum|null $navigationGroup = 'Email Templates';
    protected static ?int $navigationSort = 1;
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
                                Components\TextInput::make('name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                
                                Components\TextInput::make('key')
                                    ->required()
                                    ->helperText('Unique key for this template, e.g., order_confirmation')
                                    ->maxLength(255),

                                Components\Select::make('locale')
                                    ->options([
                                        'en' => 'English',
                                        'es' => 'Spanish',
                                        'fr' => 'French',
                                        'de' => 'German',
                                    ])
                                    ->default('en')
                                    ->required(),

                                Components\TextInput::make('subject')
                                    ->required()
                                    ->helperText('Supports tokens like ##user.name## or ##config.app.name##')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                
                                Components\Toggle::make('is_active')
                                    ->default(true),

                                Components\Select::make('theme_id')
                                    ->relationship('theme', 'name')
                                    ->nullable()
                                    ->helperText('Select a theme/layout for this template.'),
                            ])->columns(2),

                        Components\Tabs\Tab::make('Design')
                            ->schema([
                                Components\Placeholder::make('token_help')
                                    ->label('Available Tokens')
                                    ->content('Use ##model.attribute## or ##config.key.name## in your design.'),

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
                                    ->label('Visual Builder (Unlayer)'),
                                
                                Components\Hidden::make('body_html'),
                                Components\Hidden::make('body_json'),
                            ]),
                        
                        Components\Tabs\Tab::make('Preview')
                            ->schema([
                                Components\ViewField::make('preview')
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
                Tables\Columns\TextColumn::make('locale')
                    ->badge()
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
                Tables\Filters\SelectFilter::make('locale')
                    ->options([
                        'en' => 'English',
                        'es' => 'Spanish',
                        'fr' => 'French',
                        'de' => 'German',
                    ]),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\ExceptionsRelationManager::class,
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
