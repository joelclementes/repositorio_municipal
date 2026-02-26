<?php

namespace App\Http\Controllers;

use App\Models\CausaRechazo;
use Illuminate\Http\Request;

class CausaRechazoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $causas = CausaRechazo::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CausaRechazo $causaRechazo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CausaRechazo $causaRechazo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CausaRechazo $causaRechazo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CausaRechazo $causaRechazo)
    {
        //
    }
}
