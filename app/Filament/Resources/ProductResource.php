<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Forms\Components\BooleanField;
use App\Models\Product;
use App\Tables\Columns\IdColumn;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'fas-box';

    protected static ?string $label = 'منتج';
    protected static ?string $pluralLabel = 'المنتجات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('صورة المنتج')
                            ->imageEditor()
                            ->directory('products')
                            ->columnSpanFull()
                            ->image(),
                        Forms\Components\TextInput::make('name')
                            ->label('اسم المنتج')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\Group::make([
                            Forms\Components\ToggleButtons::make('type')
                                ->label('نوع المنتج')
                                ->options([
                                    'medicine' => 'دواء',
                                    'equipment' => 'معدات',
                                    'service' => 'خدمة',
                                ])
                                ->default('medicine')
                                ->inline()
                                ->grouped()
                                ->required(),
                            BooleanField::make('expiry')
                                ->reactive()
                                ->default(false)
                                ->label('صلاحية'),
                            Forms\Components\DatePicker::make('expiry_date')
                                ->columnSpan(2)
                                ->label('تاريخ الصلاحية')
                                ->default(Carbon::now()->addYear())
                                ->disabled(fn($get) => !$get('expiry'))
                                ->required(fn($get) => $get('expiry'))
                                ->displayFormat('d/m/Y'),
                        ])->columns(4)->columnSpanFull(),
                        Forms\Components\RichEditor::make('usage')
                            ->label('طريقة الإستعمال')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('dosage')
                            ->label('الجرعة')
                            ->columnSpanFull(),
                    ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make('id'),
//                Tables\Columns\ImageColumn::make('image')
//                    ->label('')
//                    ->url(fn($record) => $record->image),
                Tables\Columns\TextColumn::make('name')
                    ->label('الإسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'medicine' => 'دواء',
                        'equipment' => 'معدات',
                        'service' => 'خدمة',
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'medicine' => 'info',
                        'equipment' => 'warning',
                        'service' => 'gray',
                    })->alignCenter(),
                Tables\Columns\IconColumn::make('expiry')
                    ->label('صلاحية')
                    ->alignCenter()
                    ->boolean(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('تاريخ الصلاحية')
                    ->badge()
                    ->alignCenter()
                    ->sortable()
                    ->placeholder('-') // for nulls
                    ->color(function ($state) {
                        if (!$state) {
                            return 'gray';
                        }
                        $date = Carbon::parse($state);
                        $now = Carbon::now();

                        if ($date->isPast()) {
                            return 'gray';
                        }
                        if ($date->lessThan($now->copy()->addMonth())) {
                            return 'danger';
                        }
                        if ($date->lessThan($now->copy()->addMonths(3))) {
                            return 'warning';
                        }
                        return 'success';
                    })
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return null;
                        }
                        return Carbon::parse($state)->translatedFormat('d/m/Y');
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
