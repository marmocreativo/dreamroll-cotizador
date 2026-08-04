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
            $table->foreignId('medico_id')->nullable()->constrained('medicos')->nullOnDelete();
            $table->foreignId('hospital_id')->nullable()->constrained('hospitales')->nullOnDelete();
            $table->enum('estado', ['borrador', 'enviada', 'aceptada', 'rechazada', 'expirada'])->default('borrador');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 5, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->date('valida_hasta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};