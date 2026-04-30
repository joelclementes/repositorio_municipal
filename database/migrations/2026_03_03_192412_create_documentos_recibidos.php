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
        Schema::create('documentos_recibidos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ente_id')->constrained('entes');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('documento_id')->constrained('documentos');
            $table->foreignId('periodo_id')->constrained('periodos');

            $table->timestamps();

            $table->unique(
                ['ente_id', 'documento_id', 'periodo_id'],
                'unique_ente_documento_periodo'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_recibidos');
    }
};
