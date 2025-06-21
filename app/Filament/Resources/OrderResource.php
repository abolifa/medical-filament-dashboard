<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use DomainException;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'fas-shopping-cart';

    protected static ?string $label = 'طلب';
    protected static ?string $pluralLabel = 'الطلبات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('بيانات الطلب')
                        ->schema([
                            Forms\Components\ToggleButtons::make('flow')
                                ->label('نوع الطلب')
                                ->options([
                                    'in' => 'طلب مخزون',
                                    'out' => 'صرف لمريض',
                                    'transfer' => 'تحويل لمركز',
//                                    'adjust' => 'تعديل مخزون',
                                ])
                                ->default('out')
                                ->inline()
                                ->reactive()
                                ->required(),
                            Forms\Components\Select::make('status')
                                ->label('حالة الطلب')
                                ->options([
                                    'pending' => 'قيد الانتظار',
                                    'approved' => 'مقبول',
                                    'rejected' => 'مرفوض',
                                    'canceled' => 'ملغي',
                                ])
                                ->default('pending')
                                ->required(),
                            Forms\Components\Select::make('center_id')
                                ->label('المركز')
                                ->relationship('center', 'name')
                                ->native(false)
                                ->searchable()
                                ->preload()
                                ->required(),
                            Forms\Components\Select::make('supplier_id')
                                ->label('المورد')
                                ->relationship('supplier', 'name')
                                ->native(false)
                                ->searchable()
                                ->disabled(fn($get) => $get('flow') !== "in")
                                ->required(fn($get) => $get('flow') === "in")
                                ->preload(),
                            Forms\Components\Select::make('patient_id')
                                ->label('المريض')
                                ->relationship('patient', 'name')
                                ->native(false)
                                ->searchable()
                                ->disabled(fn($get) => $get('flow') !== "out")
                                ->required(fn($get) => $get('flow') === "out")
                                ->preload(),
                            Forms\Components\Select::make('to_center_id')
                                ->label('إلي المركز')
                                ->relationship('toCenter', 'name')
                                ->native(false)
                                ->searchable()
                                ->disabled(fn($get) => $get('flow') !== "transfer")
                                ->required(fn($get) => $get('flow') === "transfer")
                                ->preload(),
                        ])->columns(),

                    Forms\Components\Wizard\Step::make('المنتجات')
                        ->schema([
                            Forms\Components\Repeater::make('items')
                                ->relationship('items')
                                ->required()
                                ->label('')
                                ->addActionLabel('إضافة منتج')
                                ->minItems(1)
                                ->defaultItems(1)
                                ->columns()
                                ->columnSpanFull()
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('المنتج')
                                        ->native(false)
                                        ->relationship('product', 'name')
                                        ->searchable()
                                        ->preload(),
                                    Forms\Components\TextInput::make('quantity')
                                        ->label('الكمية')
                                        ->numeric()
                                        ->required(),
                                ])
                        ])
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('flow')
                    ->label('نوع الطلب')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'in' => 'طلب مخزون',
                        'out' => 'صرف لمريض',
                        'transfer' => 'تحويل لمركز',
                        default => $state,
                    })->badge()
                    ->color(
                        fn($state) => match ($state) {
                            'in' => 'info',
                            'out' => 'success',
                            'transfer' => 'warning',
                            default => 'gray',
                        }
                    ),
                Tables\Columns\TextColumn::make('source')
                    ->label('مصدر الطلب')
                    ->badge()
                    ->color('gray')
                    ->getStateUsing(
                        function ($record) {
                            if ($record->flow === 'in') {
                                return $record->supplier->name;
                            } else {
                                return $record->center->name;
                            }
                        }
                    ),
                Tables\Columns\TextColumn::make('destination')
                    ->label('مستقبل الطلب')
                    ->badge()
                    ->color('gray')
                    ->getStateUsing(
                        function ($record) {
                            if ($record->flow === 'in') {
                                return $record->center->name;
                            } elseif ($record->flow === 'transfer') {
                                return $record->toCenter->name;
                            } else {
                                return $record->patient->name;
                            }
                        }
                    ),
                Tables\Columns\TextColumn::make('items_quantity')
                    ->label('إجمالي الكمية')
                    ->alignCenter()
                    ->badge()
                    ->color('gray')
                    ->state(fn($record) => $record->items->sum('quantity'))
                    ->extraAttributes(['class' => 'cursor-pointer text-primary underline'])
                    ->action(
                        Tables\Actions\Action::make('viewItems')
                            ->label('عرض العناصر')
                            ->modalHeading(fn($record) => "العناصر في الطلب #$record->id")
                            ->modalContent(fn($record) => view('filament.modals.order-items', [
                                'items' => $record->items()->with('product')->get(),
                            ])
                            )
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('إغلاق')
                    ),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                        'canceled' => 'ملغي',
                        default => $state,
                    })->badge()
                    ->color(
                        fn($state) => match ($state) {
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        }
                    ),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('قبول')
                    ->icon('fas-check')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function (Order $record) {
                        try {
                            $record->update(['status' => 'approved']);
                            Notification::make()
                                ->title('تم اعتماد الطلب بنجاح')
                                ->success()
                                ->send();
                        } catch (DomainException $e) {
                            Notification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('reject')
                        ->label('رفض')
                        ->icon('fas-times')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status === 'pending')
                        ->action(function (Order $record) {
                            try {
                                $record->update(['status' => 'rejected']);
                            } catch (DomainException $e) {
                                Notification::make()
                                    ->title($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Tables\Actions\Action::make('cancel')
                        ->label('إلغاء')
                        ->icon('fas-ban')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status === 'approved')
                        ->action(fn($record) => $record->update(['status' => 'canceled'])),
                    Tables\Actions\Action::make('print')
                        ->label('طباعة')
                        ->icon('fas-print')
                        ->color('gray')
                        ->url(fn($record) => route('orders.print', $record))
                        ->openUrlInNewTab(),
                ]),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withSum('items as items_quantity', 'quantity');   // ← alias column
    }
}
