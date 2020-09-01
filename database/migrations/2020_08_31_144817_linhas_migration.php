<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinhasMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linhas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome');
            $table->integer('frequencia');
            $table->bigInteger('cod_empresa')->unsigned();
            $table->foreign('cod_empresa')->references('id')->on('empresas');
            $table->time('horario_inicio');
            $table->time('horario_fim');
            $table->boolean('fg_domingo')->default(false);
            $table->boolean('fg_segunda')->default(false);
            $table->boolean('fg_terca')->default(false);
            $table->boolean('fg_quarta')->default(false);
            $table->boolean('fg_quinta')->default(false);
            $table->boolean('fg_sexta')->default(false);
            $table->boolean('fg_sabado')->default(false);
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
        Schema::dropIfExists('linhas');
    }
}
