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
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("id_venta");
            $table->unsignedBigInteger("id_producto");
            $table->integer("cantidad");
            $table->decimal("subtotal",12,2);
            $table->boolean("activo")->default(true);
            $table->foreign("id_venta")->references("id")->on("ventas");
            $table->foreign("id_producto")->references("id")->on("productos")->onUpdate('cascade');
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
        Schema::dropIfExists('detalle_ventas');
    }
};
