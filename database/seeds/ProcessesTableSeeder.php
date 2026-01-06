<?php

use App\Process;
use Illuminate\Database\Seeder;
// use Symfony\Component\Process\Process;

class ProcessesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Process::truncate(); //vaciamos tabla

        Process::create([
            // 'codigo' => 06,
            'descripcion' => 'Flexo'
        ]);
        Process::create([
            // 'codigo' => 07,
            'descripcion' => 'Diecutter'
        ]);
    }
}
