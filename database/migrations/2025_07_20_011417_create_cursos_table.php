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
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("id_grado");
            $table->unsignedBigInteger("id_nivel");//primaria, bachillerato
            $table->string("seccion",100);//A, B
            $table->unsignedBigInteger("id_especialidad")->nullable();//informatica, tecnico, contabilidad
            $table->boolean("activo")->default(true);
            $table->foreign("id_grado")->references("id")->on("grados")->onUpdate("cascade");
            $table->foreign("id_nivel")->references("id")->on("niveles");
            $table->foreign("id_especialidad")->references("id")->on("especialidades");
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
        Schema::dropIfExists('cursos');
    }
};
