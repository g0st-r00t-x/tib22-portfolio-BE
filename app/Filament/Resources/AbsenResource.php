<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsenResource\Pages;
use App\Filament\Resources\AbsenResource\RelationManagers;
use App\Http\Controllers\GenerateQrCode;
use App\Models\Absen;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;

class AbsenResource extends Resource
{
    protected static ?string $model = Absen::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_dosen')
                    ->relationship('dosen', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('id_jadwal')
                    ->relationship('jadwal', 'id_jadwal')
                    ->label('Jadwal')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('id_mahasiswa')
                    ->relationship('mahasiswa', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('waktu_absen')
                    ->required(),
                Select::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpa' => 'Alpa',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa.nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('dosen.nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jadwal.hari')
                    ->sortable(),
                Tables\Columns\TextColumn::make('waktu_absen')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin' => 'warning',
                        'sakit' => 'info',
                        'alpa' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpa' => 'Alpa',
                    ]),
                Tables\Filters\SelectFilter::make('dosen')
                    ->relationship('dosen', 'nama'),
                Tables\Filters\SelectFilter::make('mahasiswa')
                    ->relationship('mahasiswa', 'nama'),
            ])
            ->headerActions([
                Action::make('scan_qr-code')
                    ->label('Scan QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn(): string => route('filament.tib-22.resources.absens.scan-qr-code')),
                Action::make('generate_qr_code')
                    ->label('Generate QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->form([
                        Checkbox::make('has_time_limit')
                            ->label('Has Time Limit')
                            ->default(false)
                            ->reactive(),

                        TextInput::make('time_limit_minutes')
                            ->label('Time Limit (Minutes)')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->visible(fn(Get $get) => $get('has_time_limit')),

                        Checkbox::make('can_extend')
                            ->label('Can Extend Time')
                            ->default(false)
                            ->reactive(),

                        TextInput::make('max_extensions')
                            ->label('Max Extensions')
                            ->helperText('Set 0 for unlimited extensions')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->visible(fn(Get $get) => $get('can_extend')),

                        TextInput::make('extension_minutes')
                            ->label('Extension Minutes')
                            ->helperText('Duration added per extension')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->visible(fn(Get $get) => $get('can_extend')),
                    ])
                    ->action(function (array $data) {
                        // Assuming text is coming from your model or you need to pass it somehow
                        $text = "Hello World!"; // Adjust this based on your needs

                        try {
                            $response = app(GenerateQrCode::class)->generate(
                                new Request($data),
                                $text
                            );

                            if ($response->getStatusCode() === 200) {
                                $responseData = json_decode($response->getContent(), true);

                                return redirect()->to(
    AbsenResource::getUrl('view-qr-code')
)->with('qr_data', $responseData);
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error Generating QR Code')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsens::route('/'),
            'create' => Pages\CreateAbsen::route('/create'),
            'edit' => Pages\EditAbsen::route('/{record}/edit'),
            'scan-qr-code' => Pages\ScanQrCode::route('/scan-qr-code'),
            'view-qr-code' => Pages\ViewQrCode::route('/view-qr-code'),
        ];
    }
}
