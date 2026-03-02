<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use NoteBrainsLab\FilamentEmailTemplates\Models\EmailTemplate;
use NoteBrainsLab\FilamentEmailTemplates\Forms\Components\UnlayerEditor;
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

    /**
     * All available Laravel events that can trigger emails.
     * Extend this list by publishing the config.
     */
    public static function getAvailableEvents(): array
    {
        return config('filament-email-templates.events', [
            \Illuminate\Auth\Events\Registered::class        => 'User Registered',
            \Illuminate\Auth\Events\Verified::class          => 'Email Verified',
            \Illuminate\Auth\Events\PasswordReset::class     => 'Password Reset',
            \Illuminate\Auth\Events\Login::class             => 'User Login',
            \Illuminate\Auth\Events\Logout::class            => 'User Logout',
            \Illuminate\Auth\Events\Failed::class            => 'Login Failed',
            \Illuminate\Auth\Events\Lockout::class           => 'Account Locked Out',
        ]);
    }

    /**
     * Available recipient types. Extend via config.
     */
    public static function getAvailableRecipients(): array
    {
        return config('filament-email-templates.recipients', [
            'user'   => 'User',
            'admin'  => 'Admin',
            'custom' => 'Custom',
        ]);
    }

    /**
     * Available attachments. Extend via config.
     */
    public static function getAvailableAttachments(): array
    {
        return config('filament-email-templates.attachments', []);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Top Section: Meta (3-column grid matching the shared image) ──
                \Filament\Schemas\Components\Grid::make(3)
                    ->schema([
                        // Column 1: Name + Event
                        \Filament\Schemas\Components\Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('This is internal name not visible to the user'),

                                Select::make('event')
                                    ->label('Event')
                                    ->options(static::getAvailableEvents())
                                    ->searchable()
                                    ->live()
                                    ->helperText(fn ($state) => static::getAvailableEvents()[$state] ?? null
                                        ? 'Fires when a ' . strtolower(static::getAvailableEvents()[$state]) . ' event occurs'
                                        : 'Select an event to trigger this email'),

                                TextInput::make('delay_minutes')
                                    ->label('Delay')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('minutes')
                                    ->helperText('Define delay that the mail should be sent after')
                                    ->placeholder('e.g. 60 for 1 hour'),
                            ])
                            ->columnSpan(1),

                        // Column 2: Recipients
                        \Filament\Schemas\Components\Section::make('Recipients')
                            ->schema([
                                CheckboxList::make('recipients')
                                    ->label('')
                                    ->options(static::getAvailableRecipients())
                                    ->helperText('Select recipients that should receive the mail'),
                            ])
                            ->columnSpan(1),

                        // Column 3: Attachments
                        \Filament\Schemas\Components\Section::make('Attachments')
                            ->schema([
                                CheckboxList::make('attachments')
                                    ->label('')
                                    ->options(static::getAvailableAttachments())
                                    ->helperText('Select attachments that should be attached to the mail'),
                            ])
                            ->columnSpan(1),
                    ]),

                // ── Bottom Section: Mail Design ──
                \Filament\Schemas\Components\Section::make('Mail')
                    ->schema([
                        TextInput::make('key')
                            ->label('Template Key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Unique key used to reference this template programmatically (e.g. auth.verify_email)')
                            ->disabled(fn ($record) => $record?->event !== null) // auto-set keys are read-only
                            ->dehydrated(true),

                        TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Use any {{variable}} that is available in your Mail class.')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        UnlayerEditor::make('unlayer_state')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                if (!$record) return null;
                                return json_encode([
                                    'design' => $record->body_json,
                                    'html'   => $record->body_html,
                                ]);
                            })
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
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
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->formatStateUsing(fn ($state) => static::getAvailableEvents()[$state] ?? $state)
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->limit(40),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options(static::getAvailableEvents())
                    ->label('Filter by Event'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit'   => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
