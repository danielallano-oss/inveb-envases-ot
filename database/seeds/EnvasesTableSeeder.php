<?php

use App\Envase;
use Illuminate\Database\Seeder;

class EnvasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Envase::truncate(); //vaciamos tabla

        Envase::create([
            // 'codigo' => 1,
            'descripcion' => 'Granel'
        ]);
        Envase::create([
            // 'codigo' => 2,
            'descripcion' => 'Pote'
        ]);
        Envase::create([
            // 'codigo' => 3,
            'descripcion' => 'Bolsa'
        ]);
        Envase::create([
            // 'codigo' => 4,
            'descripcion' => 'Tarro'
        ]);
        Envase::create([
            // 'codigo' => 5,
            'descripcion' => 'Bandeja'
        ]);
        Envase::create([
            // 'codigo' => 6,
            'descripcion' => 'Clamshell'
        ]);
        Envase::create([
            // 'codigo' => 7,
            'descripcion' => 'Botella'
        ]);
        Envase::create([
            // 'codigo' => 8,
            'descripcion' => 'Estuche'
        ]);
        Envase::create([
            // 'codigo' => 9,
            'descripcion' => 'Doypack'
        ]);
        Envase::create([
            // 'codigo' => 10,
            'descripcion' => 'Tetrapack'
        ]);
    }
}
