<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInsumoProdutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insumo_produto', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('produto_id');
            $table->unsignedBigInteger('insumo_id');
            $table->float('quantidade');
            $table->timestamps();

            $table->foreign('produto_id')
                ->references('id')
                ->on('produtos');
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
        Schema::dropIfExists('insumo_produto');
    }
}
