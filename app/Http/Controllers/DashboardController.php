<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\CotizacionProducto;
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
        $totalFacturado     = Cotizacion::whereBetween('created_at', [$desde, $hasta])->sum('total');

        // ── Serie temporal (cotizaciones por periodo) ─────
        $cotizacionesPorDia = Cotizacion::whereBetween('created_at', [$desde, $hasta])
            ->selectRaw($agrupar === 'dia'
                ? 'DATE(created_at) as periodo, COUNT(*) as total, SUM(total) as monto'
                : 'YEARWEEK(created_at, 1) as periodo, COUNT(*) as total, SUM(total) as monto'
            )
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->get()
            ->keyBy('periodo');

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

            $registro = $cotizacionesPorDia->get($key);

            $periodos->put($key, [
                'label'        => $agrupar === 'dia'
                    ? $cursor->translatedFormat('d M')
                    : 'Sem ' . $cursor->format('W'),
                'cotizaciones' => $registro->total ?? 0,
                'monto'        => $registro->monto ?? 0,
            ]);
        }

        // ── Cotizaciones por estado ────────────────────────
        $porEstado = Cotizacion::whereBetween('created_at', [$desde, $hasta])
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estados = collect(['borrador', 'enviada', 'aceptada', 'rechazada', 'expirada'])
            ->mapWithKeys(fn($estado) => [$estado => $porEstado->get($estado, 0)]);

        // ── Top productos más cotizados ────────────────────
        $topProductos = CotizacionProducto::with('producto')
            ->whereHas('cotizacion', fn($q) =>
                $q->whereBetween('created_at', [$desde, $hasta])
            )
            ->selectRaw('producto_id, SUM(cantidad) as cantidad_total, COUNT(DISTINCT cotizacion_id) as veces_cotizado')
            ->groupBy('producto_id')
            ->orderByDesc('cantidad_total')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'desde', 'hasta', 'agrupar',
            'totalCotizaciones', 'totalFacturado',
            'periodos',
            'estados',
            'topProductos'
        ));
    }
}