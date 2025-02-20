<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalResource\Pages;
use App\Filament\Resources\JadwalResource\RelationManagers;
use App\Models\Jadwal;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JadwalResource extends Resource
{
    protected static ?string $model = Jadwal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                    ])
                    ->required(),
                TimePicker::make('jam_mulai')
                ->required(),
                TimePicker::make('jam_selesai')
                ->required(),
                TextInput::make('ruangan')
                ->required()
                    ->maxLength(255),
                Select::make('id_dosen')
                ->relationship('dosen', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('id_pelajaran')
                ->relationship('pelajaran', 'nama_pelajaran')
                ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hari')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jam_mulai')
                ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_selesai')
                ->time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruangan')
                ->searchable(),
                Tables\Columns\TextColumn::make('dosen.nama')
                ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pelajaran.nama_pelajaran')
                ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                    ]),
                Tables\Filters\SelectFilter::make('dosen')
                    ->relationship('dosen', 'nama'),
                Tables\Filters\SelectFilter::make('pelajaran')
                ->relationship('pelajaran', 'nama_pelajaran'),
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
            'index' => Pages\ListJadwals::route('/'),
            'create' => Pages\CreateJadwal::route('/create'),
            'edit' => Pages\EditJadwal::route('/{record}/edit'),
        ];
    }
}
