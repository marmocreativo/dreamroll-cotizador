<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use Illuminate\Http\Request;

class InstitucionController extends Controller
{
    public function index()
    {
        $hospitales = Hospital::withCount('medicos')
            ->when(request('busqueda'), fn($q, $v) =>
                $q->where('nombre', 'like', "%{$v}%")
                ->orWhere('ciudad', 'like', "%{$v}%")
            )
            ->when(request('procedencia'), fn($q, $v) =>
                $q->where('procedencia', $v)
            )
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString(); // <-- para que la paginación conserve los filtros

        return view('instituciones.index', compact('hospitales'));
    }

    public function create()
    {
        return view('instituciones.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:150',
            'nombre_corto' => 'nullable|string|max:50',
            'procedencia'  => 'nullable|string|max:100',
            'direccion'    => 'nullable|string|max:255',
            'ciudad'       => 'nullable|string|max:100',
            'estado'       => 'nullable|string|max:100',
            'telefono'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:150',
            'notas'        => 'nullable|string',
        ]);

        Hospital::create($validated);

        return redirect()->route('instituciones.index')
            ->with('success', 'Institución creada correctamente.');
    }

    public function edit(Hospital $hospital)
    {
        return view('instituciones.form', compact('hospital'));
    }

    public function update(Request $request, Hospital $hospital)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:150',
            'nombre_corto' => 'nullable|string|max:50',
            'procedencia'  => 'nullable|string|max:100',
            'direccion'    => 'nullable|string|max:255',
            'ciudad'       => 'nullable|string|max:100',
            'estado'       => 'nullable|string|max:100',
            'telefono'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:150',
            'notas'        => 'nullable|string',
        ]);

        $hospital->update($validated);

        return redirect()->route('instituciones.index')
            ->with('success', 'Institución actualizada correctamente.');
    }

    public function destroy(Hospital $hospital)
    {
        $hospital->delete();

        return redirect()->route('instituciones.index')
            ->with('success', 'Institución eliminada correctamente.');
    }
}