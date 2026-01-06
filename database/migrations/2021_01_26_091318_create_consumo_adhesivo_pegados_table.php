<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsumoAdhesivoPegadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumo_adhesivo_pegados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('planta_id');
            $table->unsignedInteger('process_id');
            $table->unsignedInteger('consumo_adhesivo_pegado_gr_caja');
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
        Schema::dropIfExists('consumo_adhesivo_pegados');
    }
}
