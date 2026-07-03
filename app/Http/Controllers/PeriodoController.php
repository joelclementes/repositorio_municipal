<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodoController extends Controller
{


    public function index()
    {
        return view('periodos.registro');
    }

}
