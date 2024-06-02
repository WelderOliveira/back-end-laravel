<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_tipo_usuario')->truncate();
        DB::table('tb_tipo_usuario')->insert([
            ['st_descricao' => 'Pessoa Jurídica'],
            ['st_descricao' => 'Pessoa Física'],
        ]);
    }
}
