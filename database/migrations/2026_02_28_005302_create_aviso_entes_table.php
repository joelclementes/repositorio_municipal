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
        Schema::create('aviso_entes', function (Blueprint $table) {
            $table->id();

            // Relación con avisos
            $table->foreignId('aviso_id')
                ->constrained()
                ->onDelete('cascade'); // Si se borra el aviso, se borran las relaciones

            // Relación con entes
            $table->foreignId('ente_id')
                ->constrained()
                ->onDelete('cascade'); // Si se borra el ente, se borran las relaciones

            // Estados del envío
            $table->enum('estado_envio', [
                'pendiente',
                'enviado',
                'entregado',
                'leido',
                'vencido'
            ])->default('pendiente');

            // Fechas importantes para tracking
            $table->timestamp('fecha_envio')->nullable();
            $table->timestamp('fecha_lectura')->nullable();

            // Quién realizó el envío (útil para auditoría)
            $table->foreignId('enviado_por')
                ->nullable()
                ->constrained('users');
                
                $table->foreignId('leido_por')
                ->nullable()
                ->constrained('users');

            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index(['aviso_id', 'ente_id']);
            $table->index('estado_envio');
            $table->index('fecha_envio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aviso_entes');
    }
};
