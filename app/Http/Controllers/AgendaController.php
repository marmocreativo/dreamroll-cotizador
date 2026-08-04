<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\CentroAgenda;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $vista  = $request->get('vista', 'mensual');
        $fecha  = $request->get('fecha', now()->toDateString());
        $centroFiltro = $request->get('centro');

        $fechaCarbon = Carbon::parse($fecha);

        if ($vista === 'mensual') {
            $inicio = $fechaCarbon->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
            $fin    = $fechaCarbon->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
            $diasMes = collect();
            for ($d = $inicio->copy(); $d->lte($fin); $d->addDay()) {
                $diasMes->push($d->copy());
            }
            $diasSemana = collect();
        } else {
            $inicio = $fechaCarbon->copy()->startOfWeek(Carbon::MONDAY);
            $fin    = $fechaCarbon->copy()->endOfWeek(Carbon::SUNDAY);
            $diasSemana = collect();
            for ($d = $inicio->copy(); $d->lte($fin); $d->addDay()) {
                $diasSemana->push($d->copy());
            }
            $diasMes = collect();
        }

        $citas = Cita::with(['paciente', 'centro'])
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->when($centroFiltro, fn($q) => $q->where('centro_id', $centroFiltro))
            ->get();

        $citasPorDia = $citas->groupBy(fn($c) => $c->fecha->toDateString());

        $centros = CentroAgenda::where('activo', true)->orderBy('nombre')->get();

        return view('agenda.calendario', compact(
            'vista', 'fecha', 'fechaCarbon',
            'diasMes', 'diasSemana', 'citasPorDia',
            'centros', 'centroFiltro'
        ));
    }

    public function create(Request $request)
    {
        $fecha    = $request->get('fecha', now()->toDateString());
        $pacientes = Paciente::orderBy('apellido_paterno')->get();
        $centros   = CentroAgenda::where('activo', true)->orderBy('nombre')->get();

        return view('agenda.citas.create', compact('fecha', 'pacientes', 'centros'));
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'paciente_tipo'    => 'required|in:existente,nuevo',
            'paciente_id'      => 'required_if:paciente_tipo,existente|nullable|exists:pacientes,id',
            'anonimo'          => 'boolean',
            'iniciales'        => 'nullable|string|max:10',
            'nombre'           => 'nullable|string|max:100',
            'apellido_paterno' => 'nullable|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F,O',
            'whatsapp'         => 'nullable|string|max:20',
            'correo'           => 'nullable|email|max:150',
            'centro_tipo'      => 'required|in:existente,nuevo',
            'centro_id'        => 'required_if:centro_tipo,existente|nullable|exists:centros_agenda,id',
            'centro_nombre'    => 'required_if:centro_tipo,nuevo|nullable|string|max:150',
            'centro_direccion' => 'nullable|string|max:255',
            'fecha'            => 'required|date',
            'hora'             => 'required',
            'estado'           => 'required|in:programada,confirmada,realizada,cancelada',
            'notas'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            // Determinar en qué paso falló
            $errores = $validator->errors();
            $paso = 3;

            $camposPaso1 = ['paciente_tipo','paciente_id','anonimo','iniciales','nombre',
                            'apellido_paterno','apellido_materno','fecha_nacimiento','sexo',
                            'whatsapp','correo'];
            $camposPaso2 = ['centro_tipo','centro_id','centro_nombre','centro_direccion'];

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

        // Paciente
        if ($request->paciente_tipo === 'existente') {
            $paciente = Paciente::findOrFail($request->paciente_id);
        } else {
            $paciente = Paciente::create([
                'anonimo'          => $request->boolean('anonimo'),
                'iniciales'        => $request->iniciales,
                'nombre'           => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'sexo'             => $request->sexo,
                'whatsapp'         => $request->whatsapp,
                'correo'           => $request->correo,
            ]);
        }

        // Centro
        if ($request->centro_tipo === 'existente') {
            $centro = CentroAgenda::findOrFail($request->centro_id);
        } else {
            $centro = CentroAgenda::create([
                'nombre'    => $request->centro_nombre,
                'direccion' => $request->centro_direccion,
                'activo'    => true,
            ]);
        }

        // Cita
        Cita::create([
            'paciente_id' => $paciente->id,
            'centro_id'   => $centro->id,
            'fecha'       => $request->fecha,
            'hora'        => $request->hora,
            'estado'      => $request->estado,
            'notas'       => $request->notas,
        ]);

        return redirect()
            ->route('agenda.calendario', ['fecha' => $request->fecha])
            ->with('success', 'Cita registrada correctamente.');
    }

    public function show(Cita $cita)
    {
        $cita->load(['paciente', 'centro']);

        // Si es fetch (Ajax) devuelve solo el partial
        if (request()->ajax()) {
            return view('agenda.citas.detalle', compact('cita'));
        }

        return redirect()->route('agenda.calendario');
    }

    public function actualizarEstado(Request $request, Cita $cita)
    {
        $request->validate([
            'estado' => 'required|in:programada,confirmada,realizada,cancelada',
        ]);

        $cita->update(['estado' => $request->estado]);

        if ($request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Estado actualizado.');
    }

    public function edit(Cita $cita)
    {
        $cita->load(['paciente', 'centro']);
        $pacientes = Paciente::orderBy('apellido_paterno')->get();
        $centros   = CentroAgenda::where('activo', true)->orderBy('nombre')->get();

        return view('agenda.citas.edit', compact('cita', 'pacientes', 'centros'));
    }

    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'centro_id'   => 'required|exists:centros_agenda,id',
            'fecha'       => 'required|date',
            'hora'        => 'required',
            'estado'      => 'required|in:programada,confirmada,realizada,cancelada',
            'notas'       => 'nullable|string',
        ]);

        $cita->update([
            'paciente_id' => $request->paciente_id,
            'centro_id'   => $request->centro_id,
            'fecha'       => $request->fecha,
            'hora'        => $request->hora,
            'estado'      => $request->estado,
            'notas'       => $request->notas,
        ]);

        return redirect()
            ->route('agenda.calendario', ['fecha' => $cita->fecha->toDateString()])
            ->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Cita $cita)
    {
        $fecha = $cita->fecha->toDateString();
        $cita->delete();

        if (request()->ajax()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('agenda.calendario', ['fecha' => $fecha])
            ->with('success', 'Cita eliminada.');
    }

    public function enviarCita(Request $request, Cita $cita)
    {
        $request->validate(['email' => 'required|email']);

        $cita->load(['paciente', 'centro']);

        \Illuminate\Support\Facades\Mail::to($request->email)
            ->cc('agendatucita@geneticlab.mx')
            ->send(new \App\Mail\CitaMail($cita));

        return response()->json(['ok' => true]);
    }
}