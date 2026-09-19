<?php

namespace App\Http\Controllers;

use App\Services\CotizacionExcelImporter;
use Illuminate\Http\Request;

class CotizacionImportController extends Controller
{
    /**
     * Recibe el Excel, lo parsea y regresa la previsualización (NO guarda nada).
     * El usuario confirma desde el front antes de persistir.
     */
    public function previsualizar(Request $request)
    {
        $request->validate([
            'excel' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $rutaTemporal = $request->file('excel')->getRealPath();

        $resultado = (new CotizacionExcelImporter())->importar($rutaTemporal);

        return response()->json([
            'productos'    => $resultado['productos'],
            'hospedajes'   => $resultado['hospedajes'],
            'advertencias' => $resultado['advertencias'],
            'resumen' => [
                'total_productos'   => count($resultado['productos']),
                'total_hospedajes'  => count($resultado['hospedajes']),
                'grupos_detectados' => collect($resultado['productos'])
                    ->pluck('grupo')
                    ->unique()
                    ->values(),
            ],
        ]);
    }
}