<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CenterResource\Pages;
use App\Models\Center;
use App\Tables\Columns\IdColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CenterResource extends Resource
{
    protected static ?string $model = Center::class;

    protected static ?string $navigationIcon = 'fas-building';

    protected static ?string $label = 'مركز';
    protected static ?string $pluralLabel = 'المراكز';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم المركز')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('primary_phone')
                            ->label('رقم الهاتف الأساسي')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('secondary_phone')
                            ->label('رقم الهاتف الفرعي')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),
                    ])->columns(),
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('العنوان')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('street')
                            ->label('الشارع')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label('المدينة')
                            ->maxLength(255),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make('id'),
                Tables\Columns\TextColumn::make('name')
                    ->label('الإسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('primary_phone')
                    ->label('الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('المدينة')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCenters::route('/'),
            'create' => Pages\CreateCenter::route('/create'),
            'edit' => Pages\EditCenter::route('/{record}/edit'),
        ];
    }
}
