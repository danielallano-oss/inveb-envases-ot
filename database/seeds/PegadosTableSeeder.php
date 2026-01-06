<?php

use App\Pegado;
use Illuminate\Database\Seeder;

class PegadosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Pegado::truncate(); //vaciamos tabla

        Pegado::create([
            'codigo' => 1,
            'descripcion' => 'SI'
        ]);
        Pegado::create([
            'codigo' => 2,
            'descripcion' => 'NO'
        ]);
    }
}
