<?php

use App\Armado;
use Illuminate\Database\Seeder;

class ArmadosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Armado::truncate(); //vaciamos tabla

        Armado::create([
            // 'codigo' => 32,
            'descripcion' => 'Armado a Maquina'
        ]);
        Armado::create([
            // 'codigo' => 33,
            'descripcion' => 'Con/Sin Armado'
        ]);
        Armado::create([
            // 'codigo' => 34,
            'descripcion' => 'Manual'
        ]);
    }
}
