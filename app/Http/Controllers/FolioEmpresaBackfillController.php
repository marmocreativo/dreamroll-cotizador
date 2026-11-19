<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use Illuminate\Support\Facades\DB;

/**
 * Asigna folio_empresa a las cotizaciones que aún no lo tienen.
 *
 * - Por defecto es SIMULACIÓN (no escribe nada).
 * - Para aplicar los cambios: ?ejecutar=1
 * - Numera por orden de creación, por empresa (origen) y por año de creación.
 * - Es seguro correrlo varias veces: solo toca las que no tienen folio_empresa.
 */
class FolioEmpresaBackfillController extends Controller
{
    public function __invoke()
    {
        $ejecutar = request()->boolean('ejecutar');

        $pendientes = Cotizacion::whereNull('folio_empresa')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'folio', 'origen', 'created_at']);

        $contadores = [];   // prefijo => último consecutivo usado
        $asignaciones = [];

        $resultado = DB::transaction(function () use ($pendientes, &$contadores, &$asignaciones, $ejecutar) {
            foreach ($pendientes as $cot) {
                $origen  = $cot->origen ?: 'dreamroll';
                $prefijo = Cotizacion::prefijoEmpresa($origen) . '-' . $cot->created_at->format('Y') . '-';

                if (! isset($contadores[$prefijo])) {
                    // Continúa después del mayor consecutivo ya existente (si lo hay).
                    $ultimo = Cotizacion::where('folio_empresa', 'like', $prefijo . '%')
                        ->orderByRaw('LENGTH(folio_empresa) DESC')
                        ->orderByDesc('folio_empresa')
                        ->value('folio_empresa');

                    $contadores[$prefijo] = $ultimo ? (int) substr($ultimo, strlen($prefijo)) : 0;
                }

                $folioEmpresa = $prefijo . str_pad(++$contadores[$prefijo], 4, '0', STR_PAD_LEFT);

                if ($ejecutar) {
                    // query()->update evita disparar los eventos del modelo y no toca updated_at.
                    Cotizacion::whereKey($cot->id)->toBase()->update(['folio_empresa' => $folioEmpresa]);
                }

                $asignaciones[] = [
                    'id'            => $cot->id,
                    'folio_global'  => $cot->folio,
                    'origen'        => $origen,
                    'folio_empresa' => $folioEmpresa,
                ];
            }

            return count($asignaciones);
        });

        return response()->json([
            'modo'       => $ejecutar ? 'EJECUTADO' : 'SIMULACIÓN (agrega ?ejecutar=1 para aplicar)',
            'procesadas' => $resultado,
            'detalle'    => $asignaciones,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
