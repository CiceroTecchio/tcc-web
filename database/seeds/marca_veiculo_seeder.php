<?php

use Illuminate\Database\Seeder;

class marca_veiculo_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('marcas_veiculo')->insert([
            [
                'descricao_marca' => 'Volkswagen',
            ],
            [
                'descricao_marca' => 'Mercedes Benz',
            ],
            [
                'descricao_marca' => 'Marcopolo',
            ],
            [
                'descricao_marca' => 'Iveco',
            ],
            [
                'descricao_marca' => 'Volvo',
            ],
            [
                'descricao_marca' => 'Agrale',
            ],
            [
                'descricao_marca' => 'Scania',
            ],
            [
                'descricao_marca' => 'Jimbei',
            ],
            [
                'descricao_marca' => 'International',
            ],
        ]);
    }
}