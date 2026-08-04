<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Hospital;
use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function index()
    {
        $medicos = Medico::with('hospital')
            ->when(request('busqueda'), fn($q, $v) =>
                $q->where('nombre', 'like', "%{$v}%")
                ->orWhere('apellido', 'like', "%{$v}%")
                ->orWhere('email', 'like', "%{$v}%")
                ->orWhere('especialidad', 'like', "%{$v}%")
            )
            ->when(request()->has('activo') && request('activo') !== '', fn($q) =>
                $q->where('activo', request('activo'))
            )
            ->orderBy('apellido')
            ->paginate(20)
            ->withQueryString();

        return view('medicos.index', compact('medicos'));
    }

    public function create()
    {
        $hospitales = Hospital::orderBy('nombre')->get();

        return view('medicos.form', compact('hospitales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hospital_id'        => 'nullable|exists:hospitales,id',
            'prefijo'            => 'nullable|string|max:20',
            'nombre'             => 'required|string|max:100',
            'apellido'           => 'required|string|max:100',
            'email'              => 'nullable|email|max:150',
            'telefono'           => 'nullable|string|max:20',
            'especialidad'       => 'nullable|string|max:100',
            'cedula_profesional' => 'nullable|string|max:50',
            'notas'              => 'nullable|string',
            'activo'             => 'boolean',
        ]);

        Medico::create($validated);

        return redirect()->route('medicos.index')
            ->with('success', 'Médico creado correctamente.');
    }

    public function edit(Medico $medico)
    {
        $hospitales = Hospital::orderBy('nombre')->get();

        return view('medicos.form', compact('medico', 'hospitales'));
    }

    public function update(Request $request, Medico $medico)
    {
        $validated = $request->validate([
            'hospital_id'        => 'nullable|exists:hospitales,id',
            'prefijo'            => 'nullable|string|max:20',
            'nombre'             => 'required|string|max:100',
            'apellido'           => 'required|string|max:100',
            'email'              => 'nullable|email|max:150',
            'telefono'           => 'nullable|string|max:20',
            'especialidad'       => 'nullable|string|max:100',
            'cedula_profesional' => 'nullable|string|max:50',
            'notas'              => 'nullable|string',
            'activo'             => 'boolean',
        ]);

        $medico->update($validated);

        return redirect()->route('medicos.index')
            ->with('success', 'Médico actualizado correctamente.');
    }

    public function destroy(Medico $medico)
    {
        $medico->delete();

        return redirect()->route('medicos.index')
            ->with('success', 'Médico eliminado correctamente.');
    }
}