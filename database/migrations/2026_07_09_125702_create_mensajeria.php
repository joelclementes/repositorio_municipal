<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mensaje_raiz_id')
                ->nullable()
                ->constrained('mensajes')
                ->nullOnDelete();

            $table->foreignId('mensaje_padre_id')
                ->nullable()
                ->constrained('mensajes')
                ->nullOnDelete();

            $table->foreignId('remitente_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('asunto');
            $table->longText('cuerpo');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['mensaje_raiz_id', 'created_at']);
            $table->index(['remitente_id', 'created_at']);
            $table->fullText(['asunto', 'cuerpo']);
        });

        Schema::create('mensaje_destinatarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mensaje_id')
                ->constrained('mensajes')
                ->cascadeOnDelete();

            $table->foreignId('destinatario_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('estado', ['no_leido', 'leido'])
                ->default('no_leido');

            $table->timestamp('leido_at')->nullable();

            $table->timestamps();

            $table->unique(['mensaje_id', 'destinatario_id']);
            $table->index(['destinatario_id', 'estado']);
        });

        Schema::create('mensaje_archivos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mensaje_id')
                ->constrained('mensajes')
                ->cascadeOnDelete();

            $table->string('nombre_original');
            $table->string('ruta');
            $table->string('mime_type')->nullable();
            $table->string('extension', 10);
            $table->unsignedBigInteger('size')->nullable();

            $table->timestamps();

            $table->index('mensaje_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensaje_archivos');
        Schema::dropIfExists('mensaje_destinatarios');
        Schema::dropIfExists('mensajes');
    }
};
