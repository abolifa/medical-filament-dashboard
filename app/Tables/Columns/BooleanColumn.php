<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\ToggleColumn;

class BooleanColumn extends ToggleColumn
{
    public function setUp(): void
    {
        $this->label('فعال')
            ->alignCenter();
    }
}
