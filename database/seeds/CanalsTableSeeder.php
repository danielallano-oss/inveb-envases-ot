<?php

use App\Canal;
use Illuminate\Database\Seeder;

class CanalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Canal::truncate(); //vaciamos tabla

        Canal::create([
            'nombre' => 'Industria'
        ]);
        Canal::create([
            'nombre' => 'Fruta'
        ]);
    }
}
