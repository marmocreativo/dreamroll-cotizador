<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cotizacion;
use App\Models\CentroAgenda;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->get('desde')
            ? Carbon::parse($request->get('desde'))->startOfDay()
            : now()->subMonth()->startOfDay();

        $hasta = $request->get('hasta')
            ? Carbon::parse($request->get('hasta'))->endOfDay()
            : now()->endOfDay();

        $diffDias = $desde->diffInDays($hasta);
        $agrupar  = $diffDias > 60 ? 'semana' : 'dia';

        // ── Totales ──────────────────────────────────────
        $totalCotizaciones = Cotizacion::whereBetween('created_at', [$desde, $hasta])->count();
        $totalCitas        = Cita::whereBetween('created_at', [$desde, $hasta])->count();

        // ── Serie temporal ────────────────────────────────
        $cotizacionesPorDia = Cotizacion::whereBetween('created_at', [$desde, $hasta])
            ->selectRaw($agrupar === 'dia'
                ? 'DATE(created_at) as periodo, COUNT(*) as total'
                : 'YEARWEEK(created_at, 1) as periodo, COUNT(*) as total'
            )
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->pluck('total', 'periodo');

        $citasPorDia = Cita::whereBetween('created_at', [$desde, $hasta])
            ->selectRaw($agrupar === 'dia'
                ? 'DATE(created_at) as periodo, COUNT(*) as total'
                : 'YEARWEEK(created_at, 1) as periodo, COUNT(*) as total'
            )
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->pluck('total', 'periodo');

        // ── Generar períodos del rango ────────────────────
        $totalPeriodos = $agrupar === 'dia'
            ? (int) $desde->diffInDays($hasta) + 1
            : (int) $desde->diffInWeeks($hasta) + 1;

        $periodos = collect();

        for ($i = 0; $i < $totalPeriodos; $i++) {
            $cursor = $agrupar === 'dia'
                ? $desde->copy()->addDays($i)
                : $desde->copy()->addWeeks($i);

            $key = $agrupar === 'dia'
                ? $cursor->toDateString()
                : $cursor->format('oW');

            $periodos->put($key, [
                'label'        => $agrupar === 'dia'
                    ? $cursor->translatedFormat('d M')
                    : 'Sem ' . $cursor->format('W'),
                'cotizaciones' => $cotizacionesPorDia->get($key, 0),
                'citas'        => $citasPorDia->get($key, 0),
            ]);
        }

        // ── Top centros ───────────────────────────────────
        $topCentros = CentroAgenda::withCount(['citas' => fn($q) =>
                $q->whereBetween('created_at', [$desde, $hasta])
            ])
            ->having('citas_count', '>', 0)
            ->orderByDesc('citas_count')
            ->limit(5)
            ->get();

        // ── Top instituciones ─────────────────────────────
        $topInstituciones = Hospital::withCount(['cotizaciones' => fn($q) =>
                $q->whereBetween('created_at', [$desde, $hasta])
            ])
            ->having('cotizaciones_count', '>', 0)
            ->orderByDesc('cotizaciones_count')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'desde', 'hasta', 'agrupar',
            'totalCotizaciones', 'totalCitas',
            'periodos',
            'topCentros', 'topInstituciones'
        ));
    }
}