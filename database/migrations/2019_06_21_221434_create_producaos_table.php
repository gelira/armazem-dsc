<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProducaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producaos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('produto_id');
            $table->timestamp('data');
            $table->integer('lote');
            $table->integer('quantidade');
            $table->boolean('perecivel');
            $table->timestamp('expira_em');
            $table->timestamps();

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
        Schema::dropIfExists('producaos');
    }
}
