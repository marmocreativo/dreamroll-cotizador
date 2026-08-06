<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Producto;
use App\Exports\CotizacionesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CotizacionController extends Controller
{
    public function index()
    {
        $cotizaciones = Cotizacion::when(request('busqueda'), fn($q, $v) =>
                $q->where('folio', 'like', "%{$v}%")
                ->orWhere('cliente_nombre', 'like', "%{$v}%")
                ->orWhere('cliente_apellidos', 'like', "%{$v}%")
                ->orWhere('cliente_empresa', 'like', "%{$v}%")
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
        return view('cotizaciones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Paso 1 — cliente
            'cliente_prefijo'    => 'nullable|string|max:20',
            'cliente_nombre'     => 'required|string|max:100',
            'cliente_apellidos'  => 'nullable|string|max:100',
            'cliente_empresa'    => 'nullable|string|max:150',
            'cliente_telefono'   => 'nullable|string|max:20',
            'cliente_email'      => 'nullable|email|max:150',
            'cliente_direccion'  => 'nullable|string|max:255',

            // Paso 2 — productos
            'descuento'                 => 'nullable|numeric|min:0|max:100',
            'productos'                 => 'required|array|min:1',
            'productos.*.id'            => 'nullable|exists:productos,id',
            'productos.*.nombre'        => 'required|string|max:150',
            'productos.*.cantidad'      => 'required|integer|min:1',
            'productos.*.precio'        => 'required|numeric|min:0',

            // Paso 3 — entrega y condiciones
            'tiempo_entrega' => 'nullable|string|max:100',
            'condiciones'    => 'nullable|string',
            'valida_hasta'   => 'nullable|date|after:today',
            'notas'          => 'nullable|string',
        ]);

        $cotizacion = Cotizacion::create([
            'cliente_prefijo'   => $validated['cliente_prefijo'] ?? null,
            'cliente_nombre'    => $validated['cliente_nombre'],
            'cliente_apellidos' => $validated['cliente_apellidos'] ?? null,
            'cliente_empresa'   => $validated['cliente_empresa'] ?? null,
            'cliente_telefono'  => $validated['cliente_telefono'] ?? null,
            'cliente_email'     => $validated['cliente_email'] ?? null,
            'cliente_direccion' => $validated['cliente_direccion'] ?? null,
            'descuento'         => $validated['descuento'] ?? 0,
            'tiempo_entrega'    => $validated['tiempo_entrega'] ?? null,
            'condiciones'       => $validated['condiciones'] ?? null,
            'valida_hasta'      => $validated['valida_hasta'] ?? null,
            'notas'             => $validated['notas'] ?? null,
            'estado'            => 'borrador',
            'subtotal'          => 0,
            'iva'               => 0,
            'total'             => 0,
        ]);

        foreach ($validated['productos'] as $item) {
            // Si el producto no existe en catálogo, se crea (auto-alta desde el wizard)
            if (empty($item['id'])) {
                $producto = Producto::create([
                    'nombre'          => $item['nombre'],
                    'precio_unitario' => $item['precio'],
                    'activo'          => true,
                ]);
                $productoId = $producto->id;
            } else {
                $productoId = $item['id'];
            }

            $cotizacion->productos()->create([
                'producto_id'     => $productoId,
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio'],
            ]);
        }

        $cotizacion->load('productos');
        $cotizacion->recalcular();

        return redirect()->route('cotizaciones.show', $cotizacion)
            ->with('success', "Cotización {$cotizacion->folio} creada correctamente.");
    }

    public function show(Cotizacion $cotizacion)
    {
        $cotizacion->load('productos.producto');

        return view('cotizaciones.show', compact('cotizacion'));
    }

    public function edit(Cotizacion $cotizacion)
    {
        $cotizacion->load('productos.producto');

        return view('cotizaciones.edit', compact('cotizacion'));
    }

    public function update(Request $request, Cotizacion $cotizacion)
    {
        $validated = $request->validate([
            'cliente_prefijo'    => 'nullable|string|max:20',
            'cliente_nombre'     => 'required|string|max:100',
            'cliente_apellidos'  => 'nullable|string|max:100',
            'cliente_empresa'    => 'nullable|string|max:150',
            'cliente_telefono'   => 'nullable|string|max:20',
            'cliente_email'      => 'nullable|email|max:150',
            'cliente_direccion'  => 'nullable|string|max:255',
            'descuento'          => 'nullable|numeric|min:0|max:100',
            'productos'                 => 'required|array|min:1',
            'productos.*.id'            => 'nullable|exists:productos,id',
            'productos.*.nombre'        => 'required|string|max:150',
            'productos.*.cantidad'      => 'required|integer|min:1',
            'productos.*.precio'        => 'required|numeric|min:0',
            'tiempo_entrega' => 'nullable|string|max:100',
            'condiciones'    => 'nullable|string',
            'valida_hasta'   => 'nullable|date',
            'notas'          => 'nullable|string',
        ]);

        $cotizacion->update([
            'cliente_prefijo'   => $validated['cliente_prefijo'] ?? null,
            'cliente_nombre'    => $validated['cliente_nombre'],
            'cliente_apellidos' => $validated['cliente_apellidos'] ?? null,
            'cliente_empresa'   => $validated['cliente_empresa'] ?? null,
            'cliente_telefono'  => $validated['cliente_telefono'] ?? null,
            'cliente_email'     => $validated['cliente_email'] ?? null,
            'cliente_direccion' => $validated['cliente_direccion'] ?? null,
            'descuento'         => $validated['descuento'] ?? 0,
            'tiempo_entrega'    => $validated['tiempo_entrega'] ?? null,
            'condiciones'       => $validated['condiciones'] ?? null,
            'valida_hasta'      => $validated['valida_hasta'] ?? null,
            'notas'             => $validated['notas'] ?? null,
        ]);

        $cotizacion->productos()->delete();

        foreach ($validated['productos'] as $item) {
            if (empty($item['id'])) {
                $producto = Producto::create([
                    'nombre'          => $item['nombre'],
                    'precio_unitario' => $item['precio'],
                    'activo'          => true,
                ]);
                $productoId = $producto->id;
            } else {
                $productoId = $item['id'];
            }

            $cotizacion->productos()->create([
                'producto_id'     => $productoId,
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio'],
            ]);
        }

        $cotizacion->load('productos');
        $cotizacion->recalcular();

        return redirect()->route('cotizaciones.show', $cotizacion)
            ->with('success', "Cotización {$cotizacion->folio} actualizada correctamente.");
    }

    public function destroy(Cotizacion $cotizacion)
    {
        $cotizacion->productos()->delete();
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

        $cotizacion->load('productos.producto');

        \Illuminate\Support\Facades\Mail::to($request->email)
            ->send(new \App\Mail\CotizacionMail($cotizacion));

        $cotizacion->update(['estado' => 'enviada']);

        return back()->with('success', 'Cotización enviada a ' . $request->email);
    }

    /**
     * Genera el link wa.me con mensaje prellenado.
     * El PDF se descarga por separado (el usuario lo adjunta manualmente en WhatsApp).
     */
    public function enviarWhatsapp(Request $request, Cotizacion $cotizacion)
    {
        $telefono = preg_replace('/\D/', '', $request->get('telefono', $cotizacion->cliente_telefono ?? ''));

        $mensaje = "Hola {$cotizacion->cliente_nombre_completo}, te compartimos la cotización {$cotizacion->folio} "
            . "por un total de $" . number_format($cotizacion->total, 2) . " MXN. "
            . "Puedes descargar el PDF aquí: " . route('cotizaciones.pdf.ver', $cotizacion);

        $url = 'https://wa.me/' . $telefono . '?text=' . urlencode($mensaje);

        $cotizacion->update(['estado' => 'enviada']);

        return redirect()->away($url);
    }

    public function exportarExcel(Request $request)
    {
        $filtros = $request->only(['busqueda', 'estado']);

        return Excel::download(new CotizacionesExport($filtros), 'cotizaciones.xlsx');
    }

    public function descargarPdf(Cotizacion $cotizacion)
    {
        $cotizacion->load('productos.producto');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cotizacion', compact('cotizacion'));

        return $pdf->download($cotizacion->folio . '.pdf');
    }

    public function verPdf(Cotizacion $cotizacion)
    {
        $cotizacion->load('productos.producto');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cotizacion', compact('cotizacion'));

        return $pdf->stream($cotizacion->folio . '.pdf');
    }
}