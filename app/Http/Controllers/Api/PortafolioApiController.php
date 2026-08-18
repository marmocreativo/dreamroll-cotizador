<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventoResource;
use App\Models\Evento;

class PortafolioApiController extends Controller
{
    public function index()
    {
        $eventos = Evento::where('activo', true)
            ->with('galeria')
            ->orderByDesc('fecha')
            ->orderByDesc('created_at')
            ->get();

        return EventoResource::collection($eventos);
    }

    public function show(Evento $evento)
    {
        abort_unless($evento->activo, 404);

        $evento->load('galeria');

        return new EventoResource($evento);
    }
}