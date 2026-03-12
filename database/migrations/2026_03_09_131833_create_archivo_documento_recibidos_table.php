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
        Schema::create('archivo_documento_recibidos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('observaciones_ente')->nullable();
            
            $table->unsignedBigInteger('documento_recibido_id');
            $table->unsignedBigInteger('ente_id');
            $table->unsignedBigInteger('user_id');
            $table->string('tipo_recepcion',length:125);
            $table->date('fecha_cambio_estatus')->nullable();

            $table->string('usuario_revisor',length:125)->nullable();
            $table->text('observaciones_revisor')->nullable();
            $table->unsignedBigInteger('causas_rechazo_id')->nullable();
            
            $table->timestamps();

            $table->foreign('documento_recibido_id')->references('id')->on('documentos_recibidos');
            $table->foreign('ente_id')->references('id')->on('entes');
            $table->foreign('causas_rechazo_id')->references('id')->on('causas_rechazo');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivo_documento_recibidos');
    }
};
