<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('insumo_id');
            $table->float('quantidade');
            $table->float('valor_unitario');
            $table->float('icms');
            $table->float('frete');
            $table->float('ipi');
            $table->float('valor_total');
            $table->string('nf', 100);
            $table->timestamp('data_compra');
            $table->timestamp('data_entrada');
            $table->timestamps();

            $table->foreign('insumo_id')
                ->references('id')
                ->on('insumos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compras');
    }
}
