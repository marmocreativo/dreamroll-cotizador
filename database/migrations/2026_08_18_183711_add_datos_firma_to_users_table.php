<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('prefijo', 20)->nullable()->after('name');
            $table->string('apellidos', 100)->nullable()->after('prefijo');
            $table->string('puesto', 100)->nullable()->after('apellidos');
            $table->string('imagen_firma')->nullable()->after('puesto');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['prefijo', 'apellidos', 'puesto', 'imagen_firma']);
        });
    }
};