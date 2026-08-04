<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\EstudioController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AgendaPacienteController;
use App\Http\Controllers\AgendaCentroController;
use App\Http\Controllers\FusionController;

// ── Pública ───────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('welcome');

Route::get('cotizaciones/{cotizacion}/pdf/ver', [CotizacionController::class, 'verPdf'])
    ->name('cotizaciones.pdf.ver');

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/home', '/dashboard')->name('home');

    // Fusión de duplicados
    Route::prefix('fusion')->name('fusion.')->group(function () {
        Route::get('{tipo}', [FusionController::class, 'index'])->name('index');
        Route::post('{tipo}/revisar', [FusionController::class, 'revisar'])->name('revisar');
        Route::post('{tipo}/confirmar', [FusionController::class, 'confirmar'])->name('confirmar');
    });

    // Cotizaciones
    Route::get('cotizaciones/{cotizacion}/pdf', [CotizacionController::class, 'descargarPdf'])
    ->name('cotizaciones.pdf');

    
    
    Route::resource('cotizaciones', CotizacionController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->parameters(['cotizaciones' => 'cotizacion']);

    Route::patch('cotizaciones/{cotizacion}/estado', [CotizacionController::class, 'actualizarEstado'])
        ->name('cotizaciones.estado');

    Route::post('cotizaciones/{cotizacion}/enviar', [CotizacionController::class, 'enviar'])
        ->name('cotizaciones.enviar');

    // Médicos
    Route::resource('medicos', MedicoController::class);

    // Instituciones
    Route::resource('instituciones', InstitucionController::class)
        ->parameters(['instituciones' => 'hospital']);

    // Estudios
    Route::resource('estudios', EstudioController::class);

    // Agenda
    Route::prefix('agenda')->name('agenda.')->group(function () {
        Route::get('/', [AgendaController::class, 'index'])->name('calendario');

        // Nueva cita wizard
        Route::get('/nueva-cita',  [AgendaController::class, 'create'])->name('citas.create');
        Route::post('/nueva-cita', [AgendaController::class, 'store'])->name('citas.store');

        Route::get('/citas/{cita}/edit',  [AgendaController::class, 'edit'])->name('citas.edit');
        Route::put('/citas/{cita}',       [AgendaController::class, 'update'])->name('citas.update');

        Route::post('/citas/{cita}/enviar', [AgendaController::class, 'enviarCita'])->name('citas.enviar');

        // Detalle de cita (para fetch del modal)
        Route::get('/citas/{cita}',          [AgendaController::class, 'show'])->name('citas.show');
        Route::patch('/citas/{cita}/estado', [AgendaController::class, 'actualizarEstado'])->name('citas.estado');
        Route::delete('/citas/{cita}',       [AgendaController::class, 'destroy'])->name('citas.destroy');

        Route::resource('pacientes', AgendaPacienteController::class);
        Route::resource('centros',   AgendaCentroController::class)
            ->parameters(['centros' => 'centro']);
    });

    // Usuarios
    Route::resource('usuarios', UsuarioController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->parameters(['usuarios' => 'usuario']);

});

require __DIR__.'/settings.php';