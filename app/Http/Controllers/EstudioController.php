<?php

namespace App\Http\Controllers;

use App\Models\Estudio;
use Illuminate\Http\Request;

class EstudioController extends Controller
{
    public function index()
    {
        $estudios = Estudio::when(request('busqueda'), fn($q, $v) =>
                $q->where('nombre', 'like', "%{$v}%")
                ->orWhere('especimen', 'like', "%{$v}%")
                ->orWhere('area_terapeutica', 'like', "%{$v}%")
            )
            ->when(request()->has('activo') && request('activo') !== '', fn($q) =>
                $q->where('activo', request('activo'))
            )
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('estudios.index', compact('estudios'));
    }

    public function create()
    {
        return view('estudios.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'           => 'required|string|max:150',
            'especimen'        => 'nullable|string|max:100',
            'area_terapeutica' => 'nullable|string|max:100',
            'tiempo_respuesta' => 'nullable|string|max:50',
            'precio_unitario'  => 'required|numeric|min:0',
            'activo'           => 'boolean',
        ]);

        Estudio::create($validated);

        return redirect()->route('estudios.index')
            ->with('success', 'Estudio creado correctamente.');
    }

    public function edit(Estudio $estudio)
    {
        return view('estudios.form', compact('estudio'));
    }

    public function update(Request $request, Estudio $estudio)
    {
        $validated = $request->validate([
            'nombre'           => 'required|string|max:150',
            'especimen'        => 'nullable|string|max:100',
            'area_terapeutica' => 'nullable|string|max:100',
            'tiempo_respuesta' => 'nullable|string|max:50',
            'precio_unitario'  => 'required|numeric|min:0',
            'activo'           => 'boolean',
        ]);

        $estudio->update($validated);

        return redirect()->route('estudios.index')
            ->with('success', 'Estudio actualizado correctamente.');
    }

    public function destroy(Estudio $estudio)
    {
        $estudio->delete();

        return redirect()->route('estudios.index')
            ->with('success', 'Estudio eliminado correctamente.');
    }
}