<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Hospital;
use App\Models\Paciente;
use App\Models\Cotizacion;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FusionController extends Controller
{
    protected array $tipos = [
        'medicos'       => Medico::class,
        'instituciones' => Hospital::class,
        'pacientes'     => Paciente::class,
    ];

    public function index(string $tipo)
    {
        $this->validarTipo($tipo);

        $modelo    = $this->tipos[$tipo];
        $registros = $modelo::all();

        $grupos = $this->detectarDuplicados($tipo, $registros);

        return view('fusion.index', compact('tipo', 'grupos'));
    }

    public function revisar(Request $request, string $tipo)
    {
        $this->validarTipo($tipo);

        $request->validate([
            'ids'   => 'required|array|min:2',
            'ids.*' => 'required|integer',
        ]);

        $modelo    = $this->tipos[$tipo];
        $registros = $modelo::whereIn('id', $request->ids)->get();

        if ($registros->count() < 2) {
            return back()->with('error', 'Selecciona al menos dos elementos para fusionar.');
        }

        $impacto = $registros->mapWithKeys(fn ($registro) =>
            [$registro->id => $this->impacto($tipo, $registro)]
        );

        return view('fusion.revisar', compact('tipo', 'registros', 'impacto'));
    }

    public function confirmar(Request $request, string $tipo)
    {
        $this->validarTipo($tipo);

        $request->validate([
            'ids'       => 'required|array|min:2',
            'ids.*'     => 'required|integer',
            'conservar' => 'required|integer',
        ]);

        if (!in_array($request->conservar, $request->ids)) {
            return back()->with('error', 'El elemento a conservar debe estar entre los seleccionados.');
        }

        $modelo       = $this->tipos[$tipo];
        $conservarId  = $request->conservar;
        $duplicadoIds = collect($request->ids)->reject(fn ($id) => $id == $conservarId)->values();

        $conservar = $modelo::findOrFail($conservarId);

        DB::transaction(function () use ($tipo, $conservar, $duplicadoIds, $modelo) {
            foreach ($duplicadoIds as $id) {
                $duplicado = $modelo::findOrFail($id);

                $this->reasignar($tipo, $duplicado, $conservar);

                if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelo))) {
                    $duplicado->forceDelete();
                } else {
                    $duplicado->delete();
                }
            }
        });

        return redirect()->route('fusion.index', $tipo)
            ->with('success', 'Fusión completada correctamente. Se conservó el registro #' . $conservar->id);
    }

    // ── Helpers ─────────────────────────────────────────

    private function validarTipo(string $tipo): void
    {
        abort_unless(array_key_exists($tipo, $this->tipos), 404);
    }

    private function normalizar(string $texto): string
    {
        $texto = Str::of($texto)->lower()->ascii()->toString();
        $texto = preg_replace('/\b(dr|dra|sr|sra|lic|ing)\b\.?/', '', $texto);
        $texto = preg_replace('/[^a-z0-9\s]/', '', $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);

        return trim($texto);
    }

    private function claveDe(string $tipo, $registro): string
    {
        return match ($tipo) {
            'medicos'       => $this->normalizar($registro->nombre . ' ' . $registro->apellido),
            'instituciones' => $this->normalizar($registro->nombre),
            'pacientes'     => $registro->anonimo
                ? '' // los anónimos no se comparan entre sí
                : $this->normalizar(implode(' ', array_filter([
                    $registro->nombre, $registro->apellido_paterno, $registro->apellido_materno,
                ]))),
        };
    }

    private function detectarDuplicados(string $tipo, $registros)
    {
        $indexado = $registros->map(fn ($r) => [
            'registro' => $r,
            'clave'    => $this->claveDe($tipo, $r),
        ])->filter(fn ($item) => $item['clave'] !== '');

        // 1. Coincidencias exactas por clave normalizada
        $exactos = $indexado->groupBy('clave')->filter(fn ($grupo) => $grupo->count() > 1);
        $usados  = $exactos->flatten(1)->pluck('registro.id')->all();

        // 2. Coincidencias similares entre claves distintas (typos, variaciones)
        $restantes = $indexado->reject(fn ($item) => in_array($item['registro']->id, $usados))->values();

        $gruposSimilares = collect();
        $procesados = [];

        foreach ($restantes as $i => $itemA) {
            if (in_array($itemA['registro']->id, $procesados)) continue;

            $grupo = collect([$itemA]);

            foreach ($restantes as $j => $itemB) {
                if ($i === $j || in_array($itemB['registro']->id, $procesados)) continue;

                similar_text($itemA['clave'], $itemB['clave'], $porcentaje);
                $distancia = levenshtein($itemA['clave'], $itemB['clave']);

                if ($porcentaje >= 85 || $distancia <= 2) {
                    $grupo->push($itemB);
                }
            }

            if ($grupo->count() > 1) {
                foreach ($grupo as $item) {
                    $procesados[] = $item['registro']->id;
                }
                $gruposSimilares->push($grupo);
            }
        }

        $resultado = collect();

        foreach ($exactos as $grupo) {
            $resultado->push(['motivo' => 'exacto', 'items' => $grupo->pluck('registro')]);
        }

        foreach ($gruposSimilares as $grupo) {
            $resultado->push(['motivo' => 'similar', 'items' => $grupo->pluck('registro')]);
        }

        return $resultado;
    }

    private function impacto(string $tipo, $registro): array
    {
        return match ($tipo) {
            'medicos'       => [
                'cotizaciones' => Cotizacion::where('medico_id', $registro->id)->count(),
            ],
            'instituciones' => [
                'cotizaciones' => Cotizacion::where('hospital_id', $registro->id)->count(),
                'medicos'      => Medico::where('hospital_id', $registro->id)->count(),
            ],
            'pacientes'     => [
                'citas' => Cita::where('paciente_id', $registro->id)->count(),
            ],
        };
    }

    private function reasignar(string $tipo, $duplicado, $conservar): void
    {
        match ($tipo) {
            'medicos' => Cotizacion::where('medico_id', $duplicado->id)
                ->update(['medico_id' => $conservar->id]),

            'instituciones' => (function () use ($duplicado, $conservar) {
                Cotizacion::where('hospital_id', $duplicado->id)->update(['hospital_id' => $conservar->id]);
                Medico::where('hospital_id', $duplicado->id)->update(['hospital_id' => $conservar->id]);
            })(),

            'pacientes' => Cita::where('paciente_id', $duplicado->id)
                ->update(['paciente_id' => $conservar->id]),
        };
    }
}