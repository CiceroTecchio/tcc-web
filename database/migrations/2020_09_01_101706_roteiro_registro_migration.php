<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RoteiroRegistroMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roteiros_registro', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cod_linha')->unsigned();
            $table->foreign('cod_linha')->references('id')->on('linhas');
            $table->bigInteger('cod_veiculo')->unsigned();
            $table->foreign('cod_veiculo')->references('id')->on('veiculos');
            $table->bigInteger('cod_user')->unsigned();
            $table->foreign('cod_user')->references('id')->on('users');
            $table->boolean('fg_ativo')->default(false);
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
        Schema::dropIfExists('roteiro_registro');
    }
}
