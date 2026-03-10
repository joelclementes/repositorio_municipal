<?php

namespace App\Filament\Resources\CausaRechazos;

use App\Filament\Resources\CausaRechazos\Pages\CreateCausaRechazo;
use App\Filament\Resources\CausaRechazos\Pages\EditCausaRechazo;
use App\Filament\Resources\CausaRechazos\Pages\ListCausaRechazos;
use App\Filament\Resources\CausaRechazos\Schemas\CausaRechazoForm;
use App\Filament\Resources\CausaRechazos\Tables\CausaRechazosTable;
use App\Models\CausaRechazo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CausaRechazoResource extends Resource
{
    protected static ?string $model = CausaRechazo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'CausaRechazo';

    public static function form(Schema $schema): Schema
    {
        return CausaRechazoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CausaRechazosTable::configure($table);
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
            'index' => ListCausaRechazos::route('/'),
            'create' => CreateCausaRechazo::route('/create'),
            'edit' => EditCausaRechazo::route('/{record}/edit'),
        ];
    }
}
