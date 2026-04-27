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
            $table->string('regla_presentacion', 60)
                ->default('todo_el_anio')
                ->comment('Regla para validar presentación oportuna/extemporánea');
            // $table->string('periodicidad', length: 20)->nullable();
            // $table->integer('fecha_inicio')->nullable();
            // $table->integer('fecha_limite')->nullable();
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
