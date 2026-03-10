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
            $table->unsignedBigInteger('ente_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('documentos_id');
            $table->unsignedBigInteger('periodo_id');
            $table->timestamps();
            
            $table->foreign('ente_id')->references('id')->on('entes');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('documentos_id')->references('id')->on('documentos');
            $table->foreign('periodo_id')->references('id')->on('periodos');
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
