<?php

namespace App\Forms\Components;

use Filament\Forms\Components\ToggleButtons;

class BooleanField extends ToggleButtons
{
    public function setUp(): void
    {
        $this->label('فعال')
            ->boolean()
            ->inline()
            ->grouped()
            ->default(true);
    }
}
