<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::when(request('busqueda'), fn($q, $v) =>
                $q->where('empresa', 'like', "%{$v}%")
                ->orWhere('rfc', 'like', "%{$v}%")
                ->orWhere('contacto_nombre', 'like', "%{$v}%")
                ->orWhere('contacto_apellidos', 'like', "%{$v}%")
            )
            ->orderBy('empresa')
            ->paginate(20)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.form');
    }

    public function store(Request $request)
    {
        $validated = $this->validar($request);

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.form', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $this->validar($request);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }

    /**
     * Autocompletado de clientes para el wizard de cotizaciones (paso 1)
     */
    public function buscar(Request $request)
    {
        $termino = $request->get('q', '');

        $clientes = Cliente::where('activo', true)
            ->where(fn($q) =>
                $q->where('empresa', 'like', "%{$termino}%")
                ->orWhere('contacto_nombre', 'like', "%{$termino}%")
                ->orWhere('contacto_apellidos', 'like', "%{$termino}%")
                ->orWhere('rfc', 'like', "%{$termino}%")
            )
            ->orderBy('empresa')
            ->limit(10)
            ->get();

        return response()->json(
            $clientes->map(fn($c) => [
                'id'                 => $c->id,
                'empresa'            => $c->empresa,
                'rfc'                => $c->rfc,
                'direccion_fiscal'   => $c->direccion_fiscal,
                'contacto_prefijo'   => $c->contacto_prefijo,
                'contacto_nombre'    => $c->contacto_nombre,
                'contacto_apellidos' => $c->contacto_apellidos,
                'contacto_puesto'    => $c->contacto_puesto,
                'contacto_telefono'  => $c->contacto_telefono,
                'contacto_email'     => $c->contacto_email,
            ])
        );
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'empresa'            => 'required|string|max:150',
            'rfc'                => 'nullable|string|max:20',
            'direccion_fiscal'   => 'nullable|string|max:255',
            'regimen_fiscal'     => 'nullable|string|max:100',
            'uso_cfdi'           => 'nullable|string|max:100',
            'contacto_prefijo'   => 'nullable|string|max:20',
            'contacto_nombre'    => 'required|string|max:100',
            'contacto_apellidos' => 'nullable|string|max:100',
            'contacto_puesto'    => 'nullable|string|max:100',
            'contacto_telefono'  => 'nullable|string|max:20',
            'contacto_email'     => 'nullable|email|max:150',
            'activo'             => 'boolean',
        ]);
    }
}