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
            $table->unsignedBigInteger('ente_id')->comment('Id del ente que registró');
            $table->unsignedBigInteger('user_id')->comment('Id del usuario que registró');
            $table->string('tipo_recepcion', length: 125);
            $table->date('fecha_cambio_estatus')->nullable();

            $table->unsignedBigInteger('usuario_revisor')->nullable()->comment('Usuario que revisa el documento');
            $table->unsignedBigInteger('estado_id');
            $table->text('observaciones_revisor')->nullable();
            $table->unsignedBigInteger('causas_rechazo_id')->nullable();
            $table->boolean('autorizado_reenviar')->default(false);


            $table->timestamps();

            $table->foreign('documento_recibido_id')->references('id')->on('documentos_recibidos');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('ente_id')->references('id')->on('entes');
            $table->foreign('usuario_revisor')->references('id')->on('users');
            $table->foreign('estado_id')->references('id')->on('estados');
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
