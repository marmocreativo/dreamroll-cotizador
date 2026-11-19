<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->string('folio_empresa')->nullable()->unique()->after('folio');
        });
    }

    public function down(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->dropUnique(['folio_empresa']);
            $table->dropColumn('folio_empresa');
        });
    }
};
