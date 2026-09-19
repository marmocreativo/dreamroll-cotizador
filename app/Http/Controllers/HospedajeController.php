<?php

namespace App\Http\Controllers;

use App\Models\Hospedaje;
use Illuminate\Http\Request;

class HospedajeController extends Controller
{
    public function buscar(Request $request)
    {
        $termino = $request->get('q', '');

        $hospedajes = Hospedaje::where('activo', true)
            ->where('nombre', 'like', "%{$termino}%")
            ->orderBy('nombre')
            ->limit(10)
            ->get(['id', 'nombre', 'descripcion', 'costo_unitario']);

        return response()->json(
            $hospedajes->map(fn($h) => [
                'id'             => $h->id,
                'nombre'         => $h->nombre,
                'descripcion'    => $h->descripcion,
                'costo_unitario' => $h->costo_unitario,
            ])
        );
    }
}