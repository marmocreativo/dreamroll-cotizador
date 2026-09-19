<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PortafolioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CotizacionImportController;
use App\Http\Controllers\HospedajeController;

// ── Pública ───────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('welcome');
Route::get('portafolio', [PortafolioController::class, 'index'])->name('portafolio.index');
Route::get('portafolio/{evento}', [PortafolioController::class, 'show'])->name('portafolio.show');

Route::get('cotizaciones/{cotizacion}/pdf/ver', [CotizacionController::class, 'verPdf'])
    ->name('cotizaciones.pdf.ver');

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/home', '/dashboard')->name('home');

    // Cotizaciones
    Route::get('cotizaciones/{cotizacion}/pdf', [CotizacionController::class, 'descargarPdf'])
        ->name('cotizaciones.pdf');

    Route::resource('cotizaciones', CotizacionController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->parameters(['cotizaciones' => 'cotizacion']);

    Route::patch('cotizaciones/{cotizacion}/estado', [CotizacionController::class, 'actualizarEstado'])
        ->name('cotizaciones.estado');

    Route::patch('cotizaciones/{cotizacion}/firmante', [CotizacionController::class, 'actualizarFirmante'])
        ->name('cotizaciones.firmante');

    Route::post('cotizaciones/{cotizacion}/enviar', [CotizacionController::class, 'enviar'])
        ->name('cotizaciones.enviar');

    Route::get('cotizaciones/{cotizacion}/whatsapp', [CotizacionController::class, 'enviarWhatsapp'])
        ->name('cotizaciones.whatsapp');

    Route::get('cotizaciones-exportar', [CotizacionController::class, 'exportarExcel'])
        ->name('cotizaciones.exportar');

    // Autocompletado de productos para el wizard (paso 2)
    Route::get('productos/buscar', [ProductoController::class, 'buscar'])
        ->name('productos.buscar');

    // Importación de cotización desde Excel (previsualización, no guarda)
    Route::post('cotizaciones/importar-excel', [CotizacionImportController::class, 'previsualizar'])
        ->name('cotizaciones.importar-excel');

    // Autocompletado de clientes para el wizard (paso 1)
    Route::get('clientes/buscar', [ClienteController::class, 'buscar'])
        ->name('clientes.buscar');

    // Autocompletado de hospedajes para el wizard (paso 2)
    Route::get('hospedajes/buscar', [HospedajeController::class, 'buscar'])
        ->name('hospedajes.buscar');

    // Clientes
    Route::resource('clientes', ClienteController::class);

    // Productos
    Route::resource('productos', ProductoController::class);

    // Eventos (galería de portafolio)
    Route::resource('eventos', EventoController::class);
    Route::post('eventos/{evento}/imagenes', [EventoController::class, 'subirImagen'])
        ->name('eventos.imagenes.subir');
    Route::post('eventos/{evento}/imagenes/reordenar', [EventoController::class, 'reordenarImagenes'])
        ->name('eventos.imagenes.reordenar');
    Route::delete('eventos/{evento}/imagenes/{imagen}', [EventoController::class, 'eliminarImagen'])
        ->name('eventos.imagenes.eliminar');

    // Usuarios
    Route::resource('usuarios', UsuarioController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->parameters(['usuarios' => 'usuario']);

});

require __DIR__.'/settings.php';