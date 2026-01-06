<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate(); //vaciamos tabla

        //datos de prueba: uno de cada rol
        //admin:
        User::create([
            'nombre' => 'Admin',
            'apellido' => 'Istrador',
            'email' => 'admin@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '22222222-2',
            'role_id' => 1,
        ]);
        //gerente:
        User::create([
            'nombre' => 'Gerente',
            'apellido' => 'Prueba',
            'email' => 'gerente@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '33333333-3',
            'role_id' => 2,
        ]);
        // AREA DE VENTA

        // Jefe VENTA
        User::create([
            'nombre' => 'Jefe',
            'apellido' => 'Ventas',
            'email' => 'jventas@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '23748870-9',
            'role_id' => 3,
        ]);

        // Vendedor
        User::create([
            'nombre' => 'Vendedor',
            'apellido' => 'Ventas',
            'email' => 'vendedor@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '11334692-2',
            'role_id' => 4,
        ]);

        // AREA DE Desarrollo

        // Jefe Desarrollo
        User::create([
            'nombre' => 'Jefe',
            'apellido' => 'Desarrollo',
            'email' => 'jdesarrollo@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '20649380-1',
            'role_id' => 5,
        ]);

        // Ingeniero
        User::create([
            'nombre' => 'Ingeniero',
            'apellido' => 'Desarrollo',
            'email' => 'ingeniero@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '8106237-4',
            'role_id' => 6,
        ]);


        // AREA DE diseño

        // Jefe Diseñador
        User::create([
            'nombre' => 'Jefe',
            'apellido' => 'Diseño',
            'email' => 'jdiseño@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '16193907-2',
            'role_id' => 7,
        ]);

        // Diseñador
        User::create([
            'nombre' => 'Diseñador',
            'apellido' => 'Diseño',
            'email' => 'diseñador@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '9719795-4',
            'role_id' => 8,
        ]);


        // AREA Precatalogacion

        // Jefe Precatalogador
        User::create([
            'nombre' => 'Jefe',
            'apellido' => 'Precatalogador',
            'email' => 'jprecatalogador@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '24727035-3',
            'role_id' => 9,
        ]);

        // Precatalogador
        User::create([
            'nombre' => 'Precatalogador',
            'apellido' => 'Precatalogador',
            'email' => 'precatalogador@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '10554084-1',
            'role_id' => 10,
        ]);

        // AREA Catalogacion

        // Jefe Catalogador
        User::create([
            'nombre' => 'Jefe',
            'apellido' => 'Catalogador',
            'email' => 'jcatalogador@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '6334369-2',
            'role_id' => 11,
        ]);

        // Catalogador
        User::create([
            'nombre' => 'Catalogador',
            'apellido' => 'Catalogador',
            'email' => 'catalogador@inveb.cl',
            'password' => bcrypt('123123'),
            'rut' => '5068443-1',
            'role_id' => 12,
        ]);
    }
}
