<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizacion_productos', function (Blueprint $table) {
            $table->string('grupo')->nullable()->after('cotizacion_id');
            $table->string('dia')->nullable()->after('grupo');
            $table->string('lugar')->nullable()->after('dia');
            $table->decimal('iva_porcentaje', 5, 2)->nullable()->after('subtotal');
            $table->decimal('servicio_porcentaje', 5, 2)->nullable()->after('iva_porcentaje');
        });
    }

    public function down(): void
    {
        Schema::table('cotizacion_productos', function (Blueprint $table) {
            $table->dropColumn(['grupo', 'dia', 'lugar', 'iva_porcentaje', 'servicio_porcentaje']);
        });
    }
};