<?php

namespace App\Filament\Resources\Entes;

use App\Filament\Resources\Entes\Pages\CreateEnte;
use App\Filament\Resources\Entes\Pages\EditEnte;
use App\Filament\Resources\Entes\Pages\ListEntes;
use App\Filament\Resources\Entes\Schemas\EnteForm;
use App\Filament\Resources\Entes\Tables\EntesTable;
use App\Models\Ente;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EnteResource extends Resource
{
    protected static ?string $model = Ente::class;

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Ente';

    public static function form(Schema $schema): Schema
    {
        return EnteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntesTable::configure($table);
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
            'index' => ListEntes::route('/'),
            'create' => CreateEnte::route('/create'),
            'edit' => EditEnte::route('/{record}/edit'),
        ];
    }
}
