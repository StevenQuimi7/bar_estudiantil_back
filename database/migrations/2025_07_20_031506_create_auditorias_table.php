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
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->string('accion');
            $table->json('data_anterior')->nullable();
            $table->json('data_actual')->nullable();
            $table->morphs('auditable');
            $table->integer('activo')->default(1);
            $table->unsignedBigInteger('id_usuario_creacion');
            $table->foreign('id_usuario_creacion')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
