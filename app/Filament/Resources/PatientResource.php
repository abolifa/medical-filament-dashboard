<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers\VitalsRelationManager;
use App\Forms\Components\BooleanField;
use App\Models\Patient;
use App\Tables\Columns\BooleanColumn;
use App\Tables\Columns\IdColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'fas-user-injured';

    protected static ?string $label = 'مريض';
    protected static ?string $pluralLabel = 'المرضى';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('الصورة الشخصية')
                            ->alignCenter()
                            ->avatar()
                            ->imageEditor()
                            ->directory('patient-images')
                            ->columnSpanFull()
                            ->image(),
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->autocomplete(null)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('medical_file_number')
                            ->label('رقم الملف الطبي')
                            ->live()
                            ->unique(ignoreRecord: true)
                            ->autocomplete(null)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('national_id')
                            ->label('الرقم الوطني')
                            ->required()
                            ->autocomplete(false)
                            ->live()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('family_issue_number')
                            ->label('رقم قيد العائلة')
                            ->autocomplete(null)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->autocomplete(null)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('center_id')
                            ->label('المركز')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('center', 'name'),
                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->required(fn($livewire) => $livewire instanceof CreateRecord)
                            ->disabled(fn($livewire) => $livewire instanceof EditRecord)
                            ->maxLength(255),
                        BooleanField::make('active'),
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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('national_id')
                    ->label('الرقم الوطني')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('family_issue_number')
                    ->label('قيد العائلة')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('medical_file_number')
                    ->label('رقم الملف')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('center.name')
                    ->label('المركز')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->numeric()
                    ->searchable(),
                BooleanColumn::make('active'),
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
            VitalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
