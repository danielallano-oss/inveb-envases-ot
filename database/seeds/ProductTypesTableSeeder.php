<?php

use App\ProductType;
use Illuminate\Database\Seeder;

class ProductTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ProductType::truncate(); //vaciamos tabla

        ProductType::create([
            'codigo' => 23,
            'descripcion' => 'U.Vta/Set'
        ]);
        ProductType::create([
            'codigo' => 24,
            'descripcion' => 'Subset'
        ]);
    }
}
