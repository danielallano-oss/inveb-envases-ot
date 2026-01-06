<?php

use App\States as AppStates;
use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppStates::truncate(); //vaciamos tabla

        //Statess:
        AppStates::create([
            'nombre'   => 'Proceso de Ventas',
            'work_space_id' => 1
        ]);
        AppStates::create([
            'nombre'   => 'Proceso de Desarrollo',
            'work_space_id' => 2
        ]);
        AppStates::create([
            'nombre'   => 'Laboratorio',
            'work_space_id' => 2
        ]);
        AppStates::create([
            'nombre'   => 'Muestra',
            'work_space_id' => 2
        ]);
        AppStates::create([
            'nombre'   => 'Proceso de Diseño',
            'work_space_id' => 3
        ]);
        AppStates::create([
            'nombre'   => 'Proceso de Precatalogación',
            'work_space_id' => 5
        ]);
        AppStates::create([
            'nombre'   => 'Proceso de Catalogación',
            'work_space_id' => 4
        ]);
        AppStates::create([
            'nombre'   => 'OT Terminada',
            'work_space_id' => 4
        ]);
        AppStates::create([
            'nombre'   => 'Perdido',
            'work_space_id' => 1
        ]);
        AppStates::create([
            'nombre'   => 'Consulta Cliente',
            'work_space_id' => 1
        ]);
        AppStates::create([
            'nombre'   => 'OT Anulada',
            'work_space_id' => 1
        ]);
    }
}
