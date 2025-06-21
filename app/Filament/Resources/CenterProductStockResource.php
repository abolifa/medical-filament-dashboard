<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CenterProductStockResource\Pages;
use App\Models\CenterProductStock;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CenterProductStockResource extends Resource
{
    protected static ?string $model = CenterProductStock::class;

    protected static ?string $navigationIcon = 'fas-warehouse';

    protected static ?string $label = 'مخزن';
    protected static ?string $pluralLabel = 'المخزن';

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('المنتج')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color(function ($state) {
                        if ($state === 0) {
                            return 'gray';
                        } elseif ($state < 10) {
                            return 'danger';
                        } elseif ($state < 50) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('center_id')
                    ->label('المركز')
                    ->relationship('center', 'name')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('product_id')
                    ->label('المنتج')
                    ->relationship('product', 'name')
                    ->preload()
                    ->searchable(),

                Tables\Filters\Filter::make('stock_level')
                    ->label('كمية المخزون')
                    ->form([
                        Forms\Components\Select::make('level')
                            ->label('المستوى')
                            ->options([
                                'empty' => 'فارغ (0)',
                                'low' => 'منخفض (< 10)',
                                'medium' => 'متوسط (10 - 49)',
                                'high' => 'مرتفع (50+)',
                            ])
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['level'] ?? null) {
                            'empty' => $query->where('quantity', 0),
                            'low' => $query->where('quantity', '<', 10),
                            'medium' => $query->whereBetween('quantity', [10, 49]),
                            'high' => $query->where('quantity', '>=', 50),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('center_id')
                    ->relationship('center', 'name')
                    ->required(),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
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
            'index' => Pages\ListCenterProductStocks::route('/'),
        ];
    }
}
