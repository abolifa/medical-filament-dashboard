<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Forms\Components\BooleanField;
use App\Models\Doctor;
use App\Models\User;
use App\Tables\Columns\BooleanColumn;
use App\Tables\Columns\IdColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationIcon = 'fas-user-doctor';

    protected static ?string $label = 'طبيب';
    protected static ?string $pluralLabel = 'الأطباء';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Select::make('center_id')
                        ->label('المركز')
                        ->required()
                        ->relationship('center', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->reactive(),
                    Forms\Components\Select::make('user_id')
                        ->label('الحساب')
                        ->required()
                        ->options(function (callable $get) {
                            $centerId = $get('center_id');
                            if (!$centerId) return [];
                            return User::where('center_id', $centerId)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->disabled(fn(callable $get) => !$get('center_id'))
                        ->reactive(),
                    Forms\Components\TextInput::make('specialization')
                        ->label('التخصص')
                        ->maxLength(255),
                    BooleanField::make('available')
                        ->label('متاح'),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make('id'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('الحساب')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialization')
                    ->label('التخصص')
                    ->alignCenter()
                    ->searchable(),
                BooleanColumn::make('available')
                    ->label('متاح'),
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
            'index' => Pages\ListDoctors::route('/'),
        ];
    }
}
