// database/migrations/2026_05_15_000002_create_pacientes_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique(); // Siempre generado, ej: PAC-2026-00001
            $table->boolean('anonimo')->default(false);

            // Datos personales — todos opcionales
            $table->string('iniciales', 10)->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['M', 'F', 'otro'])->nullable();

            // Contacto — al menos uno requerido a nivel de app (no en DB)
            $table->string('whatsapp', 20)->nullable();
            $table->string('correo')->nullable();

            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};