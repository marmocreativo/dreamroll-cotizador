<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\Producto;
use App\Exports\CotizacionesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
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
            'cliente_id'                    => 'nullable|exists:clientes,id',
            'cliente_nuevo.empresa'         => 'required_without:cliente_id|nullable|string|max:150',
            'cliente_nuevo.rfc'             => 'nullable|string|max:20',
            'cliente_nuevo.regimen_fiscal'  => 'nullable|string|max:100',
            'cliente_nuevo.uso_cfdi'        => 'nullable|string|max:100',
            'cliente_prefijo'    => 'nullable|string|max:20',
            'cliente_nombre'     => 'required|string|max:100',
            'cliente_apellidos'  => 'nullable|string|max:100',
            'cliente_puesto'     => 'nullable|string|max:100',
            'cliente_empresa'    => 'nullable|string|max:150',
            'cliente_telefono'   => 'nullable|string|max:20',
            'cliente_email'      => 'nullable|email|max:150',
            'cliente_direccion'  => 'nullable|string|max:255',

            // Paso 2 — productos
            'fee_porcentaje'            => 'nullable|numeric|min:0|max:100',
            'descuento'                 => 'nullable|numeric|min:0|max:100',
            'productos'                 => 'required|array|min:1',
            'productos.*.id'                  => 'nullable|exists:productos,id',
            'productos.*.nombre'              => 'required|string|max:150',
            'productos.*.costo'               => 'nullable|numeric|min:0',
            'productos.*.aumento_porcentaje'  => 'nullable|numeric|min:0',
            'productos.*.cantidad'            => 'required|integer|min:1',
            'productos.*.precio'              => 'required|numeric|min:0',
            'productos.*.imagen'              => 'nullable|image|max:5120',

            // Paso 3 — entrega y condiciones
            'tiempo_entrega' => 'nullable|string|max:100',
            'condiciones'    => 'nullable|string',
            'valida_hasta'   => 'nullable|date|after:today',
            'notas'          => 'nullable|string',
        ]);

        $clienteId = $this->resolverClienteId($validated);

        $cotizacion = Cotizacion::create([
            'cliente_id'        => $clienteId,
            'cliente_prefijo'   => $validated['cliente_prefijo'] ?? null,
            'cliente_nombre'    => $validated['cliente_nombre'],
            'cliente_apellidos' => $validated['cliente_apellidos'] ?? null,
            'cliente_puesto'    => $validated['cliente_puesto'] ?? null,
            'cliente_empresa'   => $validated['cliente_empresa'] ?? null,
            'cliente_telefono'  => $validated['cliente_telefono'] ?? null,
            'cliente_email'     => $validated['cliente_email'] ?? null,
            'cliente_direccion' => $validated['cliente_direccion'] ?? null,
            'fee_porcentaje'    => $validated['fee_porcentaje'] ?? 10,
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

        foreach ($request->input('productos') as $index => $item) {
            $productoId = $this->resolverProductoId($item, $request->file("productos.{$index}.imagen"));

            $cotizacion->productos()->create([
                'producto_id'        => $productoId,
                'costo'              => $item['costo'] ?? null,
                'aumento_porcentaje' => $item['aumento_porcentaje'] ?? null,
                'cantidad'           => $item['cantidad'],
                'precio_unitario'    => $item['precio'],
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
        $cotizacion->load('productos.producto', 'cliente');

        return view('cotizaciones.edit', compact('cotizacion'));
    }

    public function update(Request $request, Cotizacion $cotizacion)
    {
        $validated = $request->validate([
            'cliente_id'                    => 'nullable|exists:clientes,id',
            'cliente_nuevo.empresa'         => 'required_without:cliente_id|nullable|string|max:150',
            'cliente_nuevo.rfc'             => 'nullable|string|max:20',
            'cliente_nuevo.regimen_fiscal'  => 'nullable|string|max:100',
            'cliente_nuevo.uso_cfdi'        => 'nullable|string|max:100',
            'cliente_prefijo'    => 'nullable|string|max:20',
            'cliente_nombre'     => 'required|string|max:100',
            'cliente_apellidos'  => 'nullable|string|max:100',
            'cliente_puesto'     => 'nullable|string|max:100',
            'cliente_empresa'    => 'nullable|string|max:150',
            'cliente_telefono'   => 'nullable|string|max:20',
            'cliente_email'      => 'nullable|email|max:150',
            'cliente_direccion'  => 'nullable|string|max:255',
            'fee_porcentaje'     => 'nullable|numeric|min:0|max:100',
            'descuento'          => 'nullable|numeric|min:0|max:100',
            'productos'                 => 'required|array|min:1',
            'productos.*.id'            => 'nullable|exists:productos,id',
            'productos.*.nombre'        => 'required|string|max:150',
            'productos.*.cantidad'      => 'required|integer|min:1',
            'productos.*.precio'        => 'required|numeric|min:0',
            'productos.*.imagen'        => 'nullable|image|max:5120',
            'tiempo_entrega' => 'nullable|string|max:100',
            'condiciones'    => 'nullable|string',
            'valida_hasta'   => 'nullable|date',
            'notas'          => 'nullable|string',
        ]);

        $clienteId = $this->resolverClienteId($validated, $cotizacion->cliente_id);

        $cotizacion->update([
            'cliente_id'        => $clienteId,
            'cliente_prefijo'   => $validated['cliente_prefijo'] ?? null,
            'cliente_nombre'    => $validated['cliente_nombre'],
            'cliente_apellidos' => $validated['cliente_apellidos'] ?? null,
            'cliente_puesto'    => $validated['cliente_puesto'] ?? null,
            'cliente_empresa'   => $validated['cliente_empresa'] ?? null,
            'cliente_telefono'  => $validated['cliente_telefono'] ?? null,
            'cliente_email'     => $validated['cliente_email'] ?? null,
            'cliente_direccion' => $validated['cliente_direccion'] ?? null,
            'fee_porcentaje'    => $validated['fee_porcentaje'] ?? 10,
            'descuento'         => $validated['descuento'] ?? 0,
            'tiempo_entrega'    => $validated['tiempo_entrega'] ?? null,
            'condiciones'       => $validated['condiciones'] ?? null,
            'valida_hasta'      => $validated['valida_hasta'] ?? null,
            'notas'             => $validated['notas'] ?? null,
        ]);

        $cotizacion->productos()->delete();

        foreach ($request->input('productos') as $index => $item) {
            $productoId = $this->resolverProductoId($item, $request->file("productos.{$index}.imagen"));

            $cotizacion->productos()->create([
                'producto_id'        => $productoId,
                'costo'              => $item['costo'] ?? null,
                'aumento_porcentaje' => $item['aumento_porcentaje'] ?? null,
                'cantidad'           => $item['cantidad'],
                'precio_unitario'    => $item['precio'],
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
            ->bcc('leopoldo.maciel@dream-roll.com')
            ->send(new \App\Mail\CotizacionMail($cotizacion, auth()->user()));

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
        $usuario = auth()->user();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cotizacion', compact('cotizacion', 'usuario'));

        return $pdf->download($cotizacion->folio . '.pdf');
    }

    public function verPdf(Cotizacion $cotizacion)
    {
        $cotizacion->load('productos.producto');
        $usuario = auth()->user();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.cotizacion', compact('cotizacion', 'usuario'));

        return $pdf->stream($cotizacion->folio . '.pdf');
    }

    /**
     * Resuelve el cliente_id final: usa el existente si viene, o crea uno
     * nuevo a partir de cliente_nuevo + los datos de contacto del paso 1.
     * $clienteIdActual permite conservar el cliente ya ligado en update()
     * cuando no se manda ni cliente_id ni cliente_nuevo (no debería pasar
     * desde el wizard, pero es un resguardo).
     */
    private function resolverClienteId(array $validated, ?int $clienteIdActual = null): ?int
    {
        if (!empty($validated['cliente_id'])) {
            return $validated['cliente_id'];
        }

        if (!empty($validated['cliente_nuevo']['empresa'] ?? null)) {
            return Cliente::create([
                'empresa'            => $validated['cliente_nuevo']['empresa'],
                'rfc'                => $validated['cliente_nuevo']['rfc'] ?? null,
                'regimen_fiscal'     => $validated['cliente_nuevo']['regimen_fiscal'] ?? null,
                'uso_cfdi'           => $validated['cliente_nuevo']['uso_cfdi'] ?? null,
                'direccion_fiscal'   => $validated['cliente_direccion'] ?? null,
                'contacto_prefijo'   => $validated['cliente_prefijo'] ?? null,
                'contacto_nombre'    => $validated['cliente_nombre'],
                'contacto_apellidos' => $validated['cliente_apellidos'] ?? null,
                'contacto_puesto'    => $validated['cliente_puesto'] ?? null,
                'contacto_telefono'  => $validated['cliente_telefono'] ?? null,
                'contacto_email'     => $validated['cliente_email'] ?? null,
                'activo'             => true,
            ])->id;
        }

        return $clienteIdActual;
    }

    /**
     * Resuelve el producto_id de un renglón del wizard: si trae id usa el
     * existente (y le actualiza la imagen si se subió una nueva); si no,
     * da de alta el producto (auto-alta desde el wizard), con imagen si vino.
     */
    private function resolverProductoId(array $item, $imagen = null): int
    {
        if (empty($item['id'])) {
            $producto = Producto::create([
                'nombre'              => $item['nombre'],
                'costo'               => $item['costo'] ?? null,
                'aumento_porcentaje'  => $item['aumento_porcentaje'] ?? null,
                'precio_unitario'     => $item['precio'],
                'activo'              => true,
                'imagen'              => $imagen ? $this->procesarImagenProducto($imagen) : null,
            ]);

            return $producto->id;
        }

        if ($imagen) {
            $producto = Producto::find($item['id']);
            if ($producto) {
                if ($producto->imagen) {
                    Storage::disk('public')->delete($producto->imagen);
                }
                $producto->update(['imagen' => $this->procesarImagenProducto($imagen)]);
            }
        }

        return $item['id'];
    }

    private function procesarImagenProducto($file): string
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