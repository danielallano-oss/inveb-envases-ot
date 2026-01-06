<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInkTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ink_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        DB::table('ink_types')->insert([
            ['descripcion' => 'Normal' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Alta gráfica: Especial' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Alta gráfica: Metalizada' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Alta gráfica: Otras' , 'created_at' => NOW(), 'updated_at' => NOW()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ink_types');
    }
}
