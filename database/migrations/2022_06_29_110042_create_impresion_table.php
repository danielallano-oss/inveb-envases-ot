<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpresionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('impresion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        DB::table('impresion')->insert([
            ['descripcion' => 'Offset' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Flexografía' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Flexografía Alta Gráfica' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Flexografía Tiro y Retiro' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Sin Impresión' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Sin Impresión (Sólo OF)' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Sin Impresión (Trazabilidad Completa)' , 'created_at' => NOW(), 'updated_at' => NOW()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('impresion');
    }
}
