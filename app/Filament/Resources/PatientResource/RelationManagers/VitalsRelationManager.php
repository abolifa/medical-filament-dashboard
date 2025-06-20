<?php

namespace App\Filament\Resources\PatientResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VitalsRelationManager extends RelationManager
{
    protected static string $relationship = 'vitals';
    protected static ?string $title = 'المؤشرات';
    protected static ?string $label = 'مؤشر';
    protected static ?string $pluralLabel = 'مؤشرات';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('recorded_at')
                    ->label('تاريخ التسجيل')
                    ->default(Carbon::now())
                    ->displayFormat('d/m/Y')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('systolic')
                    ->label('ضغط الدم الإنقباضي')
                    ->numeric(),
                Forms\Components\TextInput::make('diastolic')
                    ->label('ضغط الدم الإنبساطي')
                    ->numeric(),
                Forms\Components\TextInput::make('pulse')
                    ->label('معدل النبضات')
                    ->numeric(),
                Forms\Components\TextInput::make('temperature')
                    ->label('حرارة الجسم')
                    ->numeric(),
                Forms\Components\TextInput::make('oxygen')
                    ->label('معدل الأكسجين')
                    ->numeric(),
                Forms\Components\TextInput::make('weight')
                    ->label('وزن الجسم')
                    ->numeric(),
            ])->columns();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('patient.name')
            ->columns([
                Tables\Columns\TextColumn::make('recorded_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('d/m/Y H:i:s a')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pressure')
                    ->badge()
                    ->alignCenter()
                    ->label('ضغط الدم')
                    ->getStateUsing(fn($record) => $record->systolic . '/' . $record->diastolic)
                    ->color(function ($record) {
                        $s = $record->systolic;
                        $d = $record->diastolic;

                        if ($s < 90 || $s > 160 || $d < 60 || $d > 100) {
                            return 'danger'; // dangerous
                        }

                        if (($s >= 140) || ($d >= 90)) {
                            return 'warning'; // elevated
                        }

                        return 'success'; // normal
                    }),

                Tables\Columns\TextColumn::make('pulse')
                    ->badge()
                    ->alignCenter()
                    ->label('النبض')
                    ->color(fn($record) => match (true) {
                        $record->pulse < 60 || $record->pulse > 120 => 'danger',
                        $record->pulse >= 100 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('temperature')
                    ->badge()
                    ->alignCenter()
                    ->label('الحرارة')
                    ->color(fn($record) => match (true) {
                        $record->temperature < 36.0 || $record->temperature > 38.5 => 'danger',
                        $record->temperature >= 37.5 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('oxygen')
                    ->badge()
                    ->alignCenter()
                    ->label('الأكسجين')
                    ->color(fn($record) => match (true) {
                        $record->oxygen < 90 => 'danger',
                        $record->oxygen < 95 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('weight')
                    ->badge()
                    ->alignCenter()
                    ->label('الوزن')
                    ->color(fn($record) => match (true) {
                        $record->weight < 35 || $record->weight > 130 => 'danger',
                        $record->weight < 45 || $record->weight > 110 => 'warning',
                        default => 'success',
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

}
