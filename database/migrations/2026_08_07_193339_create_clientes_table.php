<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('empresa');
            $table->string('rfc', 20)->nullable();
            $table->string('direccion_fiscal')->nullable();
            $table->string('regimen_fiscal')->nullable();
            $table->string('uso_cfdi')->nullable();

            // Contacto principal
            $table->string('contacto_prefijo', 20)->nullable();
            $table->string('contacto_nombre');
            $table->string('contacto_apellidos')->nullable();
            $table->string('contacto_telefono', 20)->nullable();
            $table->string('contacto_email')->nullable();

            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};