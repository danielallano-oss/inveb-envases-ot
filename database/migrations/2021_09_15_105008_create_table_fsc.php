<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFsc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fsc', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->tinyInteger('codigo');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        DB::table('fsc')->insert([
            ['descripcion' => 'No' , 'codigo' => '0', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Si' , 'codigo' => '1', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Sin FSC' , 'codigo' => '2', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Logo FSC solo EEII' , 'codigo' => '3', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Logo FSC cliente y EEII' , 'codigo' => '4', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Logo FSC solo cliente' , 'codigo' => '5', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'FSC solo facturación' , 'codigo' => '6', 'created_at' => NOW(), 'updated_at' => NOW()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fsc');
    }
}
