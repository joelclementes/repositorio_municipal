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
        Schema::create('entes_revisor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ente_id');
            $table->unsignedBigInteger('revisor_id');
            $table->timestamps();

            $table->foreign('ente_id')->references('id')->on('entes')->onDelete('cascade');
            $table->foreign('revisor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entes_revisor');
    }
};
