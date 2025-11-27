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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tipo_cliente');
            // $table->unsignedBigInteger("id_curso");
            $table->string("nombres",50);
            $table->string("apellidos",50);
            // $table->string("cedula",10)->unique();
            $table->string("numero_identificacion",10)->unique();
            $table->boolean("activo")->default(true);
            // $table->foreign("id_curso")->references("id")->on("cursos");
            $table->foreign("id_tipo_cliente")->references("id")->on("tipo_clientes");
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
        Schema::dropIfExists('clientes');
    }
};
