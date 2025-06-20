<?php

namespace App\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class IdColumn extends TextColumn
{
    public function setUp(): void
    {
        $this->label('رقم')
            ->badge()
            ->searchable()
            ->color('gray');
    }
}
