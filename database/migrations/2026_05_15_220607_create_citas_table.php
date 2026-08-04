// database/migrations/2026_05_15_000003_create_citas_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->restrictOnDelete();
            $table->foreignId('centro_id')->constrained('centros_agenda')->restrictOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->enum('estado', ['programada', 'confirmada', 'realizada', 'cancelada'])->default('programada');
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fecha', 'centro_id']);
            $table->index(['paciente_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};