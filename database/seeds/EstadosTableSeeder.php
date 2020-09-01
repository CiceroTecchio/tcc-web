<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('estados')->insert([
            [
                'descricao_estado' => 'Acre',
                'uf' => 'AC'
            ],


            [
                'descricao_estado' => 'Alagoas',
                'uf' => 'AL'
            ],


            [
                'descricao_estado' => 'Amazonas',
                'uf' => 'AM'
            ],


            [
                'descricao_estado' => 'Amapá',
                'uf' => 'AP'
            ],


            [
                'descricao_estado' => 'Bahia',
                'uf' => 'BA'
            ],


            [
                'descricao_estado' => 'Ceará',
                'uf' => 'CE'
            ],


            [
                'descricao_estado' => 'Distrito Federal',
                'uf' => 'DF'
            ],


            [
                'descricao_estado' => 'Espírito Santo',
                'uf' => 'ES'
            ],


            [
                'descricao_estado' => 'Goiás',
                'uf' => 'GO'
            ],


            [
                'descricao_estado' => 'Maranhão',
                'uf' => 'MA'
            ],


            [
                'descricao_estado' => 'Minas Gerais',
                'uf' => 'MG'
            ],


            [
                'descricao_estado' => 'Mato Grosso do Sul',
                'uf' => 'MS'
            ],


            [
                'descricao_estado' => 'Mato Grosso',
                'uf' => 'MT'
            ],


            [
                'descricao_estado' => 'Pará',
                'uf' => 'PA'
            ],


            [
                'descricao_estado' => 'Paraíba',
                'uf' => 'PB'
            ],


            [
                'descricao_estado' => 'Pernambuco',
                'uf' => 'PE'
            ],


            [
                'descricao_estado' => 'Piauí',
                'uf' => 'PI'
            ],


            [
                'descricao_estado' => 'Paraná',
                'uf' => 'PR'
            ],


            [
                'descricao_estado' => 'Rio de Janeiro',
                'uf' => 'RJ'
            ],


            [
                'descricao_estado' => 'Rio Grande do Norte',
                'uf' => 'RN'
            ],


            [
                'descricao_estado' => 'Rondônia',
                'uf' => 'RO'
            ],


            [
                'descricao_estado' => 'Roraima',
                'uf' => 'RR'
            ],


            [
                'descricao_estado' => 'Rio Grande do Sul',
                'uf' => 'RS'
            ],


            [
                'descricao_estado' => 'Santa Catarina',
                'uf' => 'SC'
            ],


            [
                'descricao_estado' => 'Sergipe',
                'uf' => 'SE'
            ],


            [
                'descricao_estado' => 'São Paulo',
                'uf' => 'SP'
            ],


            [
                'descricao_estado' => 'Tocantins',
                'uf' => 'TO'
            ],
        ]);
        DB::table('estados')->update(['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }
}
