<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Medico;
use App\Models\Hospital;
use App\Models\Estudio;
use Illuminate\Http\Request;

class CotizacionController extends Controller
{
    public function index()
    {
        $cotizaciones = Cotizacion::with(['medico', 'hospital'])
            ->when(request('busqueda'), fn($q, $v) =>
                $q->where('folio', 'like', "%{$v}%")
                ->orWhereHas('medico', fn($q) =>
                    $q->where('nombre', 'like', "%{$v}%")
                        ->orWhere('apellido', 'like', "%{$v}%")
                )
                ->orWhereHas('hospital', fn($q) =>
                    $q->where('nombre', 'like', "%{$v}%")
                )
            )
            ->when(request('estado'), fn($q, $v) =>
                $q->where('estado', $v)
            )
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('cotizaciones.index', compact('cotizaciones'));
    }

    public function create()
    {
        $medicos    = Medico::where('activo', true)->orderBy('apellido')->get();
        $hospitales = Hospital::orderBy('nombre')->get();
        $estudios   = Estudio::where('activo', true)->orderBy('nombre')->get();

        return view('cotizaciones.create', compact('medicos', 'hospitales', 'estudios'));
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'medico_modo'          => 'required|in:existente,nuevo',
            'medico_id'            => 'required_if:medico_modo,existente|nullable|exists:medicos,id',
            'medico_nombre'        => 'required_if:medico_modo,nuevo|nullable|string|max:100',
            'medico_apellido'      => 'nullable|string|max:100',
            'medico_prefijo'       => 'nullable|string|max:20',
            'medico_email'         => 'nullable|email|max:150',
            'medico_telefono'      => 'nullable|string|max:20',
            'medico_especialidad'  => 'nullable|string|max:100',
            'medico_cedula'        => 'nullable|string|max:50',
            'hospital_modo'        => 'required|in:existente,nuevo',
            'hospital_id'          => 'nullable|exists:hospitales,id',
            'hospital_nombre'      => 'required_if:hospital_modo,nuevo|nullable|string|max:150',
            'hospital_procedencia' => 'nullable|string|max:50',
            'hospital_ciudad'      => 'nullable|string|max:100',
            'descuento'            => 'nullable|numeric|min:0|max:100',
            'notas'                => 'nullable|string',
            'valida_hasta'         => 'nullable|date|after:today',
            'estudios'             => 'required|array|min:1',
            'estudios.*.id'        => 'required|exists:estudios,id',
            'estudios.*.cantidad'  => 'required|integer|min:1',
            'estudios.*.precio'    => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $errores = $validator->errors();
            $paso = 3;

            $camposPaso1 = ['medico_modo','medico_id','medico_nombre'];
            $camposPaso2 = ['hospital_nombre'];

            foreach ($camposPaso1 as $campo) {
                if ($errores->has($campo)) { $paso = 1; break; }
            }
            if ($paso === 3) {
                foreach ($camposPaso2 as $campo) {
                    if ($errores->has($campo)) { $paso = 2; break; }
                }
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('paso_error', $paso);
        }

        // Médico
        if ($request->medico_modo === 'nuevo') {
            $medico = Medico::create([
                'prefijo'            => $request->medico_prefijo,
                'nombre'             => $request->medico_nombre,
                'apellido'           => $request->medico_apellido,
                'email'              => $request->medico_email,
                'telefono'           => $request->medico_telefono,
                'especialidad'       => $request->medico_especialidad,
                'cedula_profesional' => $request->medico_cedula,
                'activo'             => true,
            ]);
        } else {
            $medico = Medico::findOrFail($request->medico_id);
        }

        // Hospital
        $hospital = null;
        if ($request->hospital_modo === 'nuevo' && $request->hospital_nombre) {
            $hospital = Hospital::create([
                'nombre'      => $request->hospital_nombre,
                'procedencia' => $request->hospital_procedencia,
                'ciudad'      => $request->hospital_ciudad,
            ]);
        } elseif ($request->hospital_id) {
            $hospital = Hospital::find($request->hospital_id);
        }

        $cotizacion = Cotizacion::create([
            'medico_id'   => $medico->id,
            'hospital_id' => $hospital?->id,
            'descuento'   => $request->descuento ?? 0,
            'notas'       => $request->notas,
            'valida_hasta'=> $request->valida_hasta,
            'estado'      => 'borrador',
            'subtotal'    => 0,
            'total'       => 0,
        ]);

        foreach ($request->estudios as $item) {
            $cotizacion->estudios()->create([
                'estudio_id'      => $item['id'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio'],
            ]);
        }

        $cotizacion->load('estudios');
        $cotizacion->recalcular();

        return redirect()->route('cotizaciones.show', $cotizacion)
            ->with('success', "Cotización {$cotizacion->folio} creada correctamente.");
    }

    public function show(Cotizacion $cotizacion)
    {
        $cotizacion->load(['medico.hospital', 'hospital', 'estudios.estudio']);

        return view('cotizaciones.show', compact('cotizacion'));
    }

    public function edit(Cotizacion $cotizacion)
    {
        $cotizacion->load(['medico', 'hospital', 'estudios.estudio']);

        $medicos    = Medico::where('activo', true)->orderBy('apellido')->get();
        $hospitales = Hospital::orderBy('nombre')->get();
        $estudios   = Estudio::where('activo', true)->orderBy('nombre')->get();

        return view('cotizaciones.edit', compact('cotizacion', 'medicos', 'hospitales', 'estudios'));
    }

    public function update(Request $request, Cotizacion $cotizacion)
    {
        $validated = $request->validate([
            'medico_id'    => 'required|exists:medicos,id',
            'hospital_id'  => 'nullable|exists:hospitales,id',
            'descuento'    => 'nullable|numeric|min:0|max:100',
            'notas'        => 'nullable|string',
            'valida_hasta' => 'nullable|date',
            'estudios'     => 'required|array|min:1',
            'estudios.*.id'       => 'required|exists:estudios,id',
            'estudios.*.cantidad' => 'required|integer|min:1',
            'estudios.*.precio'   => 'required|numeric|min:0',
        ]);

        $cotizacion->update([
            'medico_id'    => $validated['medico_id'],
            'hospital_id'  => $validated['hospital_id'] ?? null,
            'descuento'    => $validated['descuento'] ?? 0,
            'notas'        => $validated['notas'] ?? null,
            'valida_hasta' => $validated['valida_hasta'] ?? null,
        ]);

        // Reemplazar renglones de estudios
        $cotizacion->estudios()->delete();

        foreach ($validated['estudios'] as $item) {
            $cotizacion->estudios()->create([
                'estudio_id'      => $item['id'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio'],
            ]);
        }

        $cotizacion->load('estudios');
        $cotizacion->recalcular();

        return redirect()->route('cotizaciones.show', $cotizacion)
            ->with('success', "Cotización {$cotizacion->folio} actualizada correctamente.");
    }

    public function destroy(Cotizacion $cotizacion)
    {
        $cotizacion->estudios()->delete();
        $cotizacion->delete();

        return redirect()->route('cotizaciones.index')
            ->with('success', 'Cotización eliminada correctamente.');
    }

    public function actualizarEstado(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'estado' => 'required|in:borrador,enviada,aceptada,rechazada,expirada',
        ]);

        $cotizacion->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function enviar(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $cotizacion->load(['medico.hospital', 'hospital', 'estudios.estudio']);

        \Illuminate\Support\Facades\Mail::to($request->email)
            ->cc('agendatucita@geneticlab.mx')
            ->send(new \App\Mail\CotizacionMail($cotizacion));

        $cotizacion->update(['estado' => 'enviada']);

        return back()->with('success', 'Cotización enviada a ' . $request->email);
    }

    public function descargarPdf(Cotizacion $cotizacion)
    {
        $cotizacion->load(['medico.hospital', 'hospital', 'estudios.estudio']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cotizacion', compact('cotizacion'));

        return $pdf->download($cotizacion->folio . '.pdf');
    }

    public function verPdf(Cotizacion $cotizacion)
    {
        $cotizacion->load(['medico.hospital', 'hospital', 'estudios.estudio']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cotizacion', compact('cotizacion'));

        return $pdf->stream($cotizacion->folio . '.pdf');
    }

}