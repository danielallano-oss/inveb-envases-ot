<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMermaConvertidorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merma_convertidoras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('planta_id');
            $table->unsignedInteger('process_id');
            $table->unsignedInteger('rubro_id');
            $table->string("porcentaje_merma_convertidora", 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merma_convertidoras');
    }
}
