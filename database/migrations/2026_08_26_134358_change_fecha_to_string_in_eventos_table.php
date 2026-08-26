<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Columna temporal para no perder los datos existentes
        Schema::table('eventos', function (Blueprint $table) {
            $table->string('fecha_texto', 255)->nullable()->after('fecha');
        });

        // 2. Backfill: convierte las fechas DATE existentes a texto legible
        DB::table('eventos')->whereNotNull('fecha')->orderBy('id')->each(function ($evento) {
            $fecha = \Carbon\Carbon::parse($evento->fecha);

            DB::table('eventos')
                ->where('id', $evento->id)
                ->update([
                    'fecha_texto' => $fecha->translatedFormat('F Y'), // ej. "Marzo 2025"
                ]);
        });

        // 3. Elimina la columna DATE original y renombra la nueva
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn('fecha');
        });

        Schema::table('eventos', function (Blueprint $table) {
            $table->renameColumn('fecha_texto', 'fecha');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->date('fecha_fecha')->nullable()->after('fecha');
        });

        DB::table('eventos')->whereNotNull('fecha')->orderBy('id')->each(function ($evento) {
            try {
                $fecha = \Carbon\Carbon::parse($evento->fecha)->toDateString();
            } catch (\Exception $e) {
                $fecha = null;
            }

            DB::table('eventos')->where('id', $evento->id)->update(['fecha_fecha' => $fecha]);
        });

        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn('fecha');
        });

        Schema::table('eventos', function (Blueprint $table) {
            $table->renameColumn('fecha_fecha', 'fecha');
        });
    }
};