<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Tables\Columns\IdColumn;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'gmdi-today-r';

    protected static ?string $label = 'نوعد';
    protected static ?string $pluralLabel = 'المواعيد';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Select::make('center_id')
                        ->label('المركز')
                        ->relationship('center', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->reactive(),
                    Forms\Components\Select::make('patient_id')
                        ->label('المريض')
                        ->relationship('patient', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required(),
                    Forms\Components\Select::make('doctor_id')
                        ->label('الدكتور')
                        ->options(function (callable $get) {
                            $centerId = $get('center_id');
                            if (!$centerId) {
                                return [];
                            }
                            return Doctor::where('center_id', $centerId)
                                ->with('user')
                                ->get()
                                ->pluck('user.name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->disabled(fn(callable $get) => !$get('center_id'))
                        ->reactive(),
                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            'pending' => 'قيد الانتظار',
                            'confirmed' => 'مؤكد',
                            'cancelled' => 'مرفوض',
                        ])->native(false)
                        ->default('pending')
                        ->required(),
                    Forms\Components\DatePicker::make('date')
                        ->label('التاريخ')
                        ->default(Carbon::now())
                        ->displayFormat('d/m/Y')
                        ->required(),
                    Forms\Components\TimePicker::make('time')
                        ->label('الوقت')
                        ->default(Carbon::now())
                        ->required(),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make('id'),
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('إسم المريض')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->label('الطبيب')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('center.name')
                    ->numeric()
                    ->label('المركز')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->label('الوقت')
                    ->time('h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'قيد الانتظار',
                        'confirmed' => 'مؤكد',
                        'cancelled' => 'مرفوض',
                    })->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                    })->alignCenter(),
                Tables\Columns\IconColumn::make('intended')
                    ->label('الحضور')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter()
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('تأكيد')
                    ->color('info')
                    ->icon('gmdi-check')
                    ->requiresConfirmation()
                    ->action(function (Appointment $record) {
                        $record->update(['status' => 'confirmed']);
                    })
                    ->visible(fn(Appointment $record) => $record->status === 'pending'),
                Tables\Actions\Action::make('cancel')
                    ->label('الغاء')
                    ->color('danger')
                    ->icon('gmdi-cancel')
                    ->requiresConfirmation()
                    ->action(function (Appointment $record) {
                        $record->update(['status' => 'cancelled']);
                    })
                    ->visible(fn(Appointment $record) => $record->status === 'pending'),
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
