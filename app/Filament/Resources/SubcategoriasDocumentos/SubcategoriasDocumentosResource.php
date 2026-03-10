<?php

namespace App\Filament\Resources\SubcategoriasDocumentos;

use App\Filament\Resources\SubcategoriasDocumentos\Pages\CreateSubcategoriasDocumentos;
use App\Filament\Resources\SubcategoriasDocumentos\Pages\EditSubcategoriasDocumentos;
use App\Filament\Resources\SubcategoriasDocumentos\Pages\ListSubcategoriasDocumentos;
use App\Filament\Resources\SubcategoriasDocumentos\Schemas\SubcategoriasDocumentosForm;
use App\Filament\Resources\SubcategoriasDocumentos\Tables\SubcategoriasDocumentosTable;
use App\Models\SubcategoriasDocumento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubcategoriasDocumentosResource extends Resource
{
    protected static ?string $model = SubcategoriasDocumento::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'SubcategoriasDocumento';

    public static function form(Schema $schema): Schema
    {
        return SubcategoriasDocumentosForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubcategoriasDocumentosTable::configure($table);
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
            'index' => ListSubcategoriasDocumentos::route('/'),
            'create' => CreateSubcategoriasDocumentos::route('/create'),
            'edit' => EditSubcategoriasDocumentos::route('/{record}/edit'),
        ];
    }
}
