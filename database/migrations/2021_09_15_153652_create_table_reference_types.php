<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReferenceTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reference_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->tinyInteger('codigo');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        DB::table('reference_types')->insert([
            ['descripcion' => 'No' , 'codigo' => '0', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Si' , 'codigo' => '1', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Sin referencia' , 'codigo' => '2', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Ref. Diseño Estructural' , 'codigo' => '3', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Ref. Diseño Gráfico' , 'codigo' => '4', 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Ref. Estructura y Grafica' , 'codigo' => '5', 'created_at' => NOW(), 'updated_at' => NOW()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reference_types');
    }
}
