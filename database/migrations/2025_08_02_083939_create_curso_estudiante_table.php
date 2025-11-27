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
        Schema::create('curso_estudiante', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cliente')->unique();
            $table->unsignedBigInteger('id_curso');
            $table->boolean("activo")->default(true);
            $table->foreign("id_curso")->references("id")->on("cursos");
            $table->foreign("id_cliente")->references("id")->on("clientes");
            $table->unsignedBigInteger('id_usuario_creacion');
            $table->timestamps();
            $table->foreign('id_usuario_creacion')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_estudiante');
    }
};
