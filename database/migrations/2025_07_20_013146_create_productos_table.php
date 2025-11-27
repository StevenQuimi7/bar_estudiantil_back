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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("id_categoria");
            $table->string("codigo",150)->unique()->index();
            $table->string("nombre",150)->index();
            $table->decimal("precio",12,2);
            $table->boolean("activo")->default(true);
            $table->foreign("id_categoria")->references("id")->on("categorias");
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
        Schema::dropIfExists('productos');
    }
};
