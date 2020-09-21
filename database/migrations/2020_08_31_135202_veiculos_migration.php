<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VeiculosMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identificador');
            $table->string('placa');
            $table->bigInteger('cod_marca')->unsigned();
            $table->foreign('cod_marca')->references('id')->on('marcas_veiculo');
            $table->bigInteger('cod_empresa')->unsigned();
            $table->foreign('cod_empresa')->references('id')->on('empresas');
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
        Schema::dropIfExists('veiculos');
    }
}
