<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaquilaServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maquila_servicios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("servicio", 191);
            $table->unsignedInteger('precio_clp_caja');
            $table->unsignedInteger('product_type_id');
            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('maquila_servicios');
    }
}
