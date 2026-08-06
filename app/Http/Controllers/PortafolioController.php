<?php

namespace App\Http\Controllers;

use App\Models\Evento;

class PortafolioController extends Controller
{
    public function index()
    {
        $eventos = Evento::where('activo', true)
            ->with('galeria')
            ->orderByDesc('fecha')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('portafolio.index', compact('eventos'));
    }

    public function show(Evento $evento)
    {
        abort_unless($evento->activo, 404);

        $evento->load('galeria');

        return view('portafolio.show', compact('evento'));
    }
}