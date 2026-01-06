<?php

use App\WorkSpace;
use Illuminate\Database\Seeder;

class WorkSpacesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    WorkSpace::truncate(); //vaciamos tabla

    //roles:
    WorkSpace::create([
      'nombre'   => 'Área de Ventas',
    ]);
    WorkSpace::create([
      'nombre'   => 'Área de Desarrollo',
    ]);
    WorkSpace::create([
      'nombre'   => 'Área de Diseño e Impresión',
    ]);
    WorkSpace::create([
      'nombre'   => 'Area de Precatalogación',
    ]);
    WorkSpace::create([
      'nombre'   => 'Area de Catalogación',
    ]);
  }
}
