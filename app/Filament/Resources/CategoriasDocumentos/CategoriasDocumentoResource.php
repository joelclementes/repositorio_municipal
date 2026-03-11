<?php

namespace App\Filament\Resources\CategoriasDocumentos;

use App\Filament\Resources\CategoriasDocumentos\Pages\CreateCategoriasDocumento;
use App\Filament\Resources\CategoriasDocumentos\Pages\EditCategoriasDocumento;
use App\Filament\Resources\CategoriasDocumentos\Pages\ListCategoriasDocumentos;
use App\Filament\Resources\CategoriasDocumentos\Schemas\CategoriasDocumentoForm;
use App\Filament\Resources\CategoriasDocumentos\Tables\CategoriasDocumentosTable;
use App\Models\CategoriasDocumento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CategoriasDocumentoResource extends Resource
{
    protected static ?string $model = CategoriasDocumento::class;

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'CategoriasDocumento';

    public static function form(Schema $schema): Schema
    {
        return CategoriasDocumentoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriasDocumentosTable::configure($table);
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
            'index' => ListCategoriasDocumentos::route('/'),
            'create' => CreateCategoriasDocumento::route('/create'),
            'edit' => EditCategoriasDocumento::route('/{record}/edit'),
        ];
    }
}
