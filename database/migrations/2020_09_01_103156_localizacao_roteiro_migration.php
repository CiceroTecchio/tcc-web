<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LocalizacaoRoteiroMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localizacao_roteiro', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cod_roteiro_registro')->unsigned();
            $table->foreign('cod_roteiro_registro')->references('id')->on('roteiros_registro');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('localizacao_roteiro');
    }
}
