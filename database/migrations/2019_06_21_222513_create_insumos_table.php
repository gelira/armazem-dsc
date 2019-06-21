<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInsumosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insumos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('unidade_id');
            $table->unsignedBigInteger('fornecedor_id');
            $table->string('descricao', 50);
            $table->boolean('materia_prima');
            $table->float('estoque');
            $table->timestamps();

            $table->foreign('unidade_id')
                ->references('id')
                ->on('unidades');
            $table->foreign('fornecedor_id')
                ->references('id')
                ->on('fornecedors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insumos');
    }
}
