<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // database/migrations/[timestamp]_create_avisos_table.php
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('tipo_aviso')->comment('Ejemplo: "Aviso", "Invitación", "Exhorto"');
            $table->text('texto');
            $table->boolean('activo')->default(true);
            $table->string('archivo')->nullable();
            $table->timestamp('fecha_publicacion')->nullable(); // ¿Cuándo se publica?
            $table->timestamp('fecha_expiracion')->nullable(); // ¿Hasta cuándo es válido?
            $table->foreignId('creado_por')->constrained('users');
            $table->timestamps();
            $table->softDeletes(); // Para no perder datos históricos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avisos');
    }
};
