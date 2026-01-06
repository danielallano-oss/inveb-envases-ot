<?php

use App\ManagementType;
use Illuminate\Database\Seeder;

class ManagementTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ManagementType::truncate(); //vaciamos tabla

        //tipos de gestion:
        ManagementType::create([
            'nombre'   => 'Cambio de Estado',
        ]);
        ManagementType::create([
            'nombre'   => 'Consulta',
        ]);
        ManagementType::create([
            'nombre'   => 'Archivo',
        ]);
        ManagementType::create([
            'nombre'   => 'Log de Cambios',
        ]);
    }
}
