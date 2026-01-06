<?php

use App\Rayado;
use Illuminate\Database\Seeder;

class RayadosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Rayado::truncate(); //vaciamos tabla

        Rayado::create([
            'codigo' => 000,
            'descripcion' => 'Sin Rayado'
        ]);
        Rayado::create([
            'codigo' => 001,
            'descripcion' => 'Macho y Hembra'
        ]);
        Rayado::create([
            'codigo' => 002,
            'descripcion' => 'Manual'
        ]);
        Rayado::create([
            'codigo' => 003,
            'descripcion' => 'Punto a Punto'
        ]);
        Rayado::create([
            'codigo' => 004,
            'descripcion' => 'Visagra'
        ]);
    }
}
