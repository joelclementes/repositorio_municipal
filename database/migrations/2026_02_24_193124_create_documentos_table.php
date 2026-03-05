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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre', length: 255)->unique();
            $table->unsignedBigInteger('subcategoria_id');
            $table->string('periodicidad', length: 20);
            $table->integer('fecha_limite');
            $table->string('formato', length: 20)->comment('PDF ó XLSX');
            $table->timestamps();

            $table->foreign('subcategoria_id')->references('id')->on('subcategorias_documentos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
