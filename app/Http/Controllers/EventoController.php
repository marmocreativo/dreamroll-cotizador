<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\GaleriaEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class EventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::when(request('busqueda'), fn($q, $v) =>
                $q->where('titulo', 'like', "%{$v}%")
                ->orWhere('cliente', 'like', "%{$v}%")
            )
            ->with('galeria')
            ->orderByDesc('fecha')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('eventos.index', compact('eventos'));
    }

    public function create()
    {
        return view('eventos.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'      => 'required|string|max:150',
            'descripcion' => 'required|string',
            'fecha'       => 'nullable|string|max:100',
            'lugar'       => 'nullable|string|max:150',
            'cliente'     => 'nullable|string|max:150',
            'activo'      => 'boolean',
        ]);

        $evento = Evento::create($validated);

        return redirect()->route('eventos.show', $evento)
            ->with('success', 'Evento creado correctamente. Ahora puedes agregar imágenes a la galería.');
    }

    public function show(Evento $evento)
    {
        $evento->load('galeria');

        return view('eventos.show', compact('evento'));
    }

    public function edit(Evento $evento)
    {
        return view('eventos.form', compact('evento'));
    }

    public function update(Request $request, Evento $evento)
    {
        $validated = $request->validate([
            'titulo'      => 'required|string|max:150',
            'descripcion' => 'required|string',
            'fecha'       => 'nullable|string|max:100',
            'lugar'       => 'nullable|string|max:150',
            'cliente'     => 'nullable|string|max:150',
            'activo'      => 'boolean',
        ]);

        $evento->update($validated);

        return redirect()->route('eventos.show', $evento)
            ->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(Evento $evento)
    {
        foreach ($evento->galeria as $imagen) {
            Storage::disk('public')->delete($imagen->imagen);
        }

        $evento->delete();

        return redirect()->route('eventos.index')
            ->with('success', 'Evento eliminado correctamente.');
    }

    /**
     * Sube UNA imagen a la galería (llamado vía AJAX, una petición por archivo).
     */
    public function subirImagen(Request $request, Evento $evento)
    {
        $request->validate([
            'imagen' => 'required|image|max:10240',
        ]);

        $procesada = $this->procesarImagen($request->file('imagen'));

        $siguienteOrden = $evento->galeria()->max('orden');
        $siguienteOrden = is_null($siguienteOrden) ? 0 : $siguienteOrden + 1;

        $imagen = $evento->galeria()->create([
            'imagen' => $procesada,
            'orden'  => $siguienteOrden,
        ]);

        return response()->json([
            'id'         => $imagen->id,
            'imagen_url' => $imagen->imagen_url,
            'orden'      => $imagen->orden,
        ]);
    }

    /**
     * Actualiza el orden de todas las imágenes de un evento (drag & drop).
     */
    public function reordenarImagenes(Request $request, Evento $evento)
    {
        $validated = $request->validate([
            'orden'   => 'required|array',
            'orden.*' => 'integer|exists:galeria_eventos,id',
        ]);

        foreach ($validated['orden'] as $index => $imagenId) {
            GaleriaEvento::where('id', $imagenId)
                ->where('evento_id', $evento->id)
                ->update(['orden' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function eliminarImagen(Evento $evento, GaleriaEvento $imagen)
    {
        abort_if($imagen->evento_id !== $evento->id, 404);

        Storage::disk('public')->delete($imagen->imagen);
        $imagen->delete();

        return response()->json(['success' => true]);
    }

    private function procesarImagen($file): string
    {
        $imagen = Image::decode($file)
            ->scaleDown(width: 1024, height: 1024);

        $nombreArchivo = 'eventos/' . Str::uuid() . '.webp';

        Storage::disk('public')->put(
            $nombreArchivo,
            $imagen->encodeUsingFileExtension('webp', quality: 80)
        );

        return $nombreArchivo;
    }
}