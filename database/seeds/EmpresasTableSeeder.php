<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('empresas')->insert([
            'nome' => 'Cadê o Busão?',
            'razao_social' => 'Cadê o Busão?',
            'cnpj' => '99.999.999.9999-99',
            'cod_cidade' => 2883,
            'endereco' => 'Rua das Palmas, 105',
            'fg_ativo' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
