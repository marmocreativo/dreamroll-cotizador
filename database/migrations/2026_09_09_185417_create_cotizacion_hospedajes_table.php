<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizacion_hospedajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacion_id')->constrained('cotizaciones')->cascadeOnDelete();
            $table->foreignId('hospedaje_id')->nullable()->constrained('hospedajes')->nullOnDelete();

            $table->string('nombre');
            $table->string('checkin')->nullable();
            $table->string('checkout')->nullable();
            $table->unsignedInteger('noches')->default(0);
            $table->unsignedInteger('habitaciones')->default(0);

            $table->decimal('costo_unitario', 10, 2)->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);

            $table->decimal('ish_porcentaje', 5, 2)->nullable();
            $table->decimal('iva_porcentaje', 5, 2)->nullable();

            $table->decimal('resort_fee', 10, 2)->nullable();
            $table->decimal('bell_boys', 10, 2)->nullable();
            $table->decimal('camaristas', 10, 2)->nullable();

            // Cargos no previstos que vengan en el Excel (columnas desconocidas entre "costo unit" y "total")
            $table->json('cargos_adicionales')->nullable();

            $table->decimal('total', 10, 2)->default(0);

            $table->text('notas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_hospedajes');
    }
};