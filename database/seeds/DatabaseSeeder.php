<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            WorkSpacesTableSeeder::class,
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            ProductTypesTableSeeder::class,
            CanalsTableSeeder::class,
            ArmadosTableSeeder::class,
            EnvasesTableSeeder::class,
            MercadosTableSeeder::class,
            PegadosTableSeeder::class,
            ProcessesTableSeeder::class,
            RayadosTableSeeder::class,
            ManagementTypeTableSeeder::class,
        ]);
    }
}
