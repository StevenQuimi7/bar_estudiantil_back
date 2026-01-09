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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("id_cliente");
            $table->decimal("total_pagar",12,2);
            $table->decimal("descuento_credito",12,2);
            $table->decimal("total_venta",12,2);
            $table->enum("estado_gestion",['PENDIENTE','PROCESO','PAGADO','ANULADO
            '])->default('PENDIENTE');
            $table->boolean("activo")->default(true);
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
        Schema::dropIfExists('ventas');
    }
};
