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
        Schema::create('movimiento_creditos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_credito_cliente');
            $table->enum('tipo',['ABONO','CONSUMO','REVERSO','ANULACION-VENTA']);
            $table->decimal('monto',12,2);
            $table->string('descripcion',250)->nullable();
            $table->decimal('saldo_anterior',12,2);//cabecera saldo
            $table->decimal('saldo_actual',12,2);
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('id_usuario_creacion');
            $table->unsignedBigInteger('id_venta')->nullable();
            $table->unsignedBigInteger('id_venta_consolidada')->nullable();
            $table->foreign('id_usuario_creacion')->references('id')->on('users');
            $table->foreign('id_credito_cliente')->references('id')->on('credito_clientes');
            $table->foreign('id_venta')->references('id')->on('ventas');
            $table->foreign('id_venta_consolidada')->references('id')->on('venta_consolidadas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_creditos');
    }
};
