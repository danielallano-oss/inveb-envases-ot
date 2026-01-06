<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProtectionType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('protection_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        DB::table('protection_type')->insert([
            ['descripcion' => 'Valor STD' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Completa  ( Superior )' ,'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Completa + Esquinero de Carton' , 'created_at' => NOW(), 'updated_at' => NOW()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('protection_type');
    }
}
