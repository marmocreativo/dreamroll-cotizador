<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Illuminate\Http\Request;

class AgendaPacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::withCount('citas')
            ->when(request('busqueda'), fn($q, $v) =>
                $q->where('nombre', 'like', "%{$v}%")
                ->orWhere('apellido_paterno', 'like', "%{$v}%")
                ->orWhere('iniciales', 'like', "%{$v}%")
                ->orWhere('folio', 'like', "%{$v}%")
            )
            ->when(request()->has('anonimo') && request('anonimo') !== '', fn($q) =>
                $q->where('anonimo', request('anonimo'))
            )
            ->orderBy('apellido_paterno')
            ->paginate(20)
            ->withQueryString();

        return view('agenda.pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return view('agenda.pacientes.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anonimo'          => 'boolean',
            'iniciales'        => 'nullable|string|max:10',
            'nombre'           => 'nullable|string|max:100',
            'apellido_paterno' => 'nullable|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'edad'             => 'nullable|integer|min:0|max:120',
            'sexo'             => 'nullable|in:M,F,O',
            'whatsapp'         => 'nullable|string|max:20',
            'correo'           => 'nullable|email|max:150',
            'notas'            => 'nullable|string',
        ]);

        $this->validarIdentidad($request);

        Paciente::create($validated);

        return redirect()->route('agenda.pacientes.index')
            ->with('success', 'Paciente registrado correctamente.');
    }

    public function edit(Paciente $paciente)
    {
        return view('agenda.pacientes.form', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $validated = $request->validate([
            'anonimo'          => 'boolean',
            'iniciales'        => 'nullable|string|max:10',
            'nombre'           => 'nullable|string|max:100',
            'apellido_paterno' => 'nullable|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F,O',
            'whatsapp'         => 'nullable|string|max:20',
            'correo'           => 'nullable|email|max:150',
            'notas'            => 'nullable|string',
        ]);

        $this->validarIdentidad($request);

        $paciente->update($validated);

        return redirect()->route('agenda.pacientes.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();

        return redirect()->route('agenda.pacientes.index')
            ->with('success', 'Paciente eliminado correctamente.');
    }

    // Si el paciente no es anónimo debe tener al menos nombre o iniciales
    private function validarIdentidad(Request $request): void
    {
        if (!$request->boolean('anonimo')) {
            if (empty($request->nombre) && empty($request->iniciales)) {
                abort(422, 'Un paciente no anónimo debe tener nombre o iniciales.');
            }
        }
    }
}