<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMermaCorrugadorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merma_corrugadoras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("porcentaje_merma_corrugadora", 200);
            $table->unsignedInteger('planta_id');
            $table->unsignedInteger('carton_id');
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
        Schema::dropIfExists('merma_corrugadoras');
    }
}
