<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Cícero Ismael Tecchio',
            'email' => 'cicero@cadeobusao.com',
            'cpf' => '109.859.269-70',
            'cod_empresa' => 1,
            'fg_ativo' => 1,
            'fg_admin' => 1,
            'password' => Hash::make('123cadeobusao!@#'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
