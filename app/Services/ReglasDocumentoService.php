<?php

namespace App\Services;
use Carbon\Carbon;

class ReglasDocumentoService
{
    // Servicio para determinar si un documento es oportuno o extemporáneo según su periodicidad y fecha límite
    private $fechaActual;

public function __construct()
{
    $this->fechaActual = Carbon::now();
}


    public function oportunidad($periodicidad, $fechaLimite)
    {

        // Obtenemos la fecha actual
        $fechaActual = Carbon::now();

        // función para obtener el número de mes actual
        $añoActual = $fechaActual->year;
        $mesActual = $fechaActual->month;
        $diaLimite = $fechaLimite;

        // Funciòn para convertir $añoActual y $mesActual a una fecha Carbon con el día límite
        $fechaLimiteCarbon = Carbon::create($añoActual, $mesActual, $diaLimite);

        // Lógica para determinar si el documento es oportuno o extemporáneo
        if ($fechaActual->lessThanOrEqualTo($fechaLimiteCarbon)) {
            return 'oportuno';
        } else {
            return 'extemporáneo';
        }

    }
}
