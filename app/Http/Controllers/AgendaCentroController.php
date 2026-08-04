<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CentroAgenda;
use Illuminate\Http\Request;

class AgendaCentroController extends Controller
{
    public function index()
    {
        $centros = CentroAgenda::withCount('citas')
            ->when(request('busqueda'), fn($q, $v) =>
                $q->where('nombre', 'like', "%{$v}%")
            )
            ->when(request()->has('activo') && request('activo') !== '', fn($q) =>
                $q->where('activo', request('activo'))
            )
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('agenda.centros.index', compact('centros'));
    }

    public function create()
    {
        return view('agenda.centros.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:150',
            'direccion' => 'nullable|string|max:255',
            'activo'    => 'boolean',
        ]);

        CentroAgenda::create($validated);

        return redirect()->route('agenda.centros.index')
            ->with('success', 'Centro creado correctamente.');
    }

    public function edit(CentroAgenda $centro)
    {
        return view('agenda.centros.form', compact('centro'));
    }

    public function update(Request $request, CentroAgenda $centro)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:150',
            'direccion' => 'nullable|string|max:255',
            'activo'    => 'boolean',
        ]);

        $centro->update($validated);

        return redirect()->route('agenda.centros.index')
            ->with('success', 'Centro actualizado correctamente.');
    }

    public function destroy(CentroAgenda $centro)
    {
        $centro->delete();

        return redirect()->route('agenda.centros.index')
            ->with('success', 'Centro eliminado correctamente.');
    }
}