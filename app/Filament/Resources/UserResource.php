<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Tables\Columns\IdColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'fas-users';

    protected static ?string $label = 'حساب';
    protected static ?string $pluralLabel = 'الحسابات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\TextInput::make('name')
                        ->label('الاسم')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('البريد الالكتروني')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('رقم الهاتف')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->label('كلمة المرور')
                        ->password()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('center_id')
                        ->label('المركز')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->relationship('center', 'name'),
                    Forms\Components\Select::make('roles')
                        ->label('الصلاحيات')
                        ->native(false)
                        ->relationship('roles', 'name')
                        ->multiple(false)
                        ->preload()
                        ->searchable(),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make('id'),
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('الصلاحيات')
                    ->badge()
                    ->alignCenter()
                    ->sortable()
                    ->color('gray'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
