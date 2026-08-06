<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique()->nullable();

            // Datos del cliente (paso 1) — campos sueltos, sin tabla propia
            $table->string('cliente_prefijo', 20)->nullable();
            $table->string('cliente_nombre');
            $table->string('cliente_apellidos')->nullable();
            $table->string('cliente_empresa')->nullable();
            $table->string('cliente_telefono', 20)->nullable();
            $table->string('cliente_email')->nullable();
            $table->string('cliente_direccion')->nullable();

            // Totales (paso 2)
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 5, 2)->default(0);   // porcentaje
            $table->decimal('iva', 10, 2)->default(0);        // monto calculado 16%
            $table->decimal('total', 10, 2)->default(0);

            // Entrega y condiciones (paso 3)
            $table->string('tiempo_entrega')->nullable();
            $table->text('condiciones')->nullable();
            $table->date('valida_hasta')->nullable();

            $table->enum('estado', ['borrador', 'enviada', 'aceptada', 'rechazada', 'expirada'])
                ->default('borrador');

            $table->text('notas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};