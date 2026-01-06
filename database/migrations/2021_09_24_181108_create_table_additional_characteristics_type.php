<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdditionalCharacteristicsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_characteristics_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        DB::table('additional_characteristics_type')->insert([
            ['descripcion' => 'Sin Entrada' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Display FD' ,'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Display TP' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Display Cinta' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Display Prepicado' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Display Caja Master' , 'created_at' => NOW(), 'updated_at' => NOW()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_characteristics_type');
    }
}
