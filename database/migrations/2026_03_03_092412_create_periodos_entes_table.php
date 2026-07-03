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
    Schema::create('periodos_entes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ente_id')->constrained('entes');
        $table->foreignId('periodo_id')->constrained('periodos');
        $table->date('fecha_inicio')->nullable();
        $table->date('fecha_fin')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();

        $table->unique(['ente_id', 'periodo_id'], 'unique_ente_periodo');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodos_entes');
    }
};
