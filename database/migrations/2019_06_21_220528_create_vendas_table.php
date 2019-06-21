<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('produto_id');
            $table->timestamp('data');
            $table->integer('quantidade');
            $table->float('preco_unitario');
            $table->float('base_lucro');
            $table->float('preco_total');
            $table->string('nf', 100);
            $table->timestamps();

            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes');
            $table->foreign('produto_id')
                ->references('id')
                ->on('produtos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendas');
    }
}
