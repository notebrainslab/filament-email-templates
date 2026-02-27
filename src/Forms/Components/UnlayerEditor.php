<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Forms\Components;

use Filament\Schemas\Components\Field;

class UnlayerEditor extends Field
{
    protected string $view = 'filament-email-templates::forms.components.unlayer-editor';

    public function setUp(): void
    {
        parent::setUp();
        
        $this->afterStateHydrated(static function (UnlayerEditor $component, $state): void {
            if (! is_array($state)) {
                $component->state(['json' => null, 'html' => null]);
            }
        });

        $this->dehydrateStateUsing(static function ($state) {
            return is_array($state) ? $state : ['json' => null, 'html' => null];
        });
    }
}
