<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::when(request('busqueda'), fn($q, $v) =>
                $q->where('nombre', 'like', "%{$v}%")
                ->orWhere('descripcion', 'like', "%{$v}%")
            )
            ->when(request()->has('activo') && request('activo') !== '', fn($q) =>
                $q->where('activo', request('activo'))
            )
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'              => 'required|string|max:150',
            'descripcion'         => 'nullable|string|max:255',
            'costo'               => 'nullable|numeric|min:0',
            'aumento_porcentaje'  => 'nullable|numeric|min:0',
            'precio_unitario'     => 'required|numeric|min:0',
            'imagen'              => 'nullable|image|max:5120',
            'activo'              => 'boolean',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $this->procesarImagen($request->file('imagen'));
        }

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return view('productos.form', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre'              => 'required|string|max:150',
            'descripcion'         => 'nullable|string|max:255',
            'costo'               => 'nullable|numeric|min:0',
            'aumento_porcentaje'  => 'nullable|numeric|min:0',
            'precio_unitario'     => 'required|numeric|min:0',
            'imagen'              => 'nullable|image|max:5120',
            'activo'              => 'boolean',
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $validated['imagen'] = $this->procesarImagen($request->file('imagen'));
        }

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Autocompletado de productos para el wizard de cotizaciones (paso 2)
     */
    public function buscar(Request $request)
    {
        $termino = $request->get('q', '');

        $productos = Producto::where('activo', true)
            ->where('nombre', 'like', "%{$termino}%")
            ->orderBy('nombre')
            ->limit(10)
            ->get(['id', 'nombre', 'costo', 'aumento_porcentaje', 'precio_unitario', 'imagen']);

        return response()->json(
            $productos->map(fn($p) => [
                'id'                 => $p->id,
                'nombre'             => $p->nombre,
                'costo'              => $p->costo,
                'aumento_porcentaje' => $p->aumento_porcentaje,
                'precio_unitario'    => $p->precio_unitario,
                'imagen_url'         => $p->imagen_url,
            ])
        );
    }

    private function procesarImagen($file): string
    {
        $imagen = Image::decode($file)
            ->cover(600, 600);

        $nombreArchivo = 'productos/' . Str::uuid() . '.webp';

        Storage::disk('public')->put(
            $nombreArchivo,
            $imagen->encodeUsingFileExtension('webp', quality: 80)
        );

        return $nombreArchivo;
    }
}