<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('costo', 10, 2)->nullable()->after('nombre');
            $table->decimal('aumento_porcentaje', 5, 2)->nullable()->after('costo');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['costo', 'aumento_porcentaje']);
        });
    }
};