<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RevisorController extends Controller
{
    
    public function index(Request $request)
    {
        return view('revisores.avances');
    }
}
