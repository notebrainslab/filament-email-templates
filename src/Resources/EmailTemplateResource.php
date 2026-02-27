<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Forms\Components\UnlayerEditor;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';
    protected static ?string $navigationGroup = 'Settings';

    public static function getModelLabel(): string
    {
        return __('Email Template');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Email Templates');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('event_class')
                                    ->required()
                                    ->helperText('Full class name of the Laravel event. E.g., App\Events\OrderPlaced')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('subject')
                                    ->required()
                                    ->helperText('Supports Blade variables from the event.')
                                    ->maxLength(255),
                                
                                Forms\Components\TagsInput::make('recipients')
                                    ->helperText('Add static emails or Blade variables like {{ $user->email }}')
                                    ->placeholder('Add recipient...')
                                    ->required(),
                                
                                Forms\Components\TagsInput::make('cc')
                                    ->helperText('Add CC emails or Blade variables.'),
                                    
                                Forms\Components\TagsInput::make('bcc')
                                    ->helperText('Add BCC emails or Blade variables.'),

                                Forms\Components\TagsInput::make('attachments')
                                    ->helperText('Add full paths or Blade expressions like {{ storage_path($invoice->path) }}'),

                                Forms\Components\TextInput::make('delay_minutes')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Delay sending by minutes after event is fired (0 for immediate).'),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Design')
                            ->schema([
                                Forms\Components\ViewField::make('unlayer_state')
                                    ->view('filament-email-templates::forms.components.unlayer-editor')
                                    // Save state using custom logic since it maps to two DB fields (json, html)
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
                                
                                Forms\Components\Hidden::make('body_html'),
                                Forms\Components\Hidden::make('body_json'),
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
                Tables\Columns\TextColumn::make('event_class')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject'),
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
