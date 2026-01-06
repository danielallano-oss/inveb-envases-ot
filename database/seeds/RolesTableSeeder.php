<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Role::truncate(); //vaciamos tabla

    //roles:
    Role::create([
      'nombre'   => 'Administrador',
    ]);
    Role::create([
      'nombre'   => 'Gerente',
    ]);
    Role::create([
      'nombre'   => 'Jefe de Ventas',
      'work_space_id' => 1
    ]);
    Role::create([
      'nombre'   => 'Vendedor',
      'work_space_id' => 1
    ]);
    Role::create([
      'nombre'   => 'Jefe de Desarrollo',
      'work_space_id' => 2
    ]);
    Role::create([
      'nombre'   => 'Ingeniero',
      'work_space_id' => 2
    ]);
    Role::create([
      'nombre'   => 'Jefe de Diseño e Impresión',
      'work_space_id' => 3
    ]);
    Role::create([
      'nombre'   => 'Diseñador',
      'work_space_id' => 3
    ]);
    Role::create([
      'nombre'   => 'Jefe de Precatalogación',
      'work_space_id' => 4
    ]);
    Role::create([
      'nombre'   => 'Precatalogador',
      'work_space_id' => 4
    ]);
    Role::create([
      'nombre'   => 'Jefe de Catalogación',
      'work_space_id' => 5
    ]);
    Role::create([
      'nombre'   => 'Catalogador',
      'work_space_id' => 5
    ]);
    Role::create([
      'nombre'   => 'Super Administrador',
      'work_space_id' => 7
    ]);
  }
}
