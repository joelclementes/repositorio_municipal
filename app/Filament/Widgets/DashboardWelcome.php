<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardWelcome extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Bienvenido', '')
                ->description('Sistema de Administración de Catálogos de la Información Financiera y Obras Municipales')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('primary')
        ];
    }
}