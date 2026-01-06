<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRecubrimientoTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recubrimiento_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->tinyInteger('codigo');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        DB::table('recubrimiento_types')->insert([
            ['descripcion' => 'No Aplica' , 'codigo' => '0', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Cera' , 'codigo' => '1', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Barniz Exterior' , 'codigo' => '2', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Cera Interior' , 'codigo' => '3', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Cera Exterior' , 'codigo' => '4', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Cera Ambas Caras' , 'codigo' => '5', 'created_at' => NOW(), 'updated_at' => NOW()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recubrimiento_types');
    }
}
