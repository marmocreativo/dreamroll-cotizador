<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->decimal('fee_agencia', 10, 2)->default(0.00)->after('subtotal');
        });
    }

    public function destroy(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->dropColumn('fee_agencia');
        });
    }
};