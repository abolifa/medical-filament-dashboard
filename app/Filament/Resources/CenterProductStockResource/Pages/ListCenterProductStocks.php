<?php

namespace App\Filament\Resources\CenterProductStockResource\Pages;

use App\Filament\Resources\CenterProductStockResource;
use Filament\Resources\Pages\ListRecords;

class ListCenterProductStocks extends ListRecords
{
    protected static string $resource = CenterProductStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}
