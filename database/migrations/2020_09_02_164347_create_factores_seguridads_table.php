<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactoresSeguridadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factores_seguridads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('rubro_id')->nullable();
            $table->unsignedInteger('envase_id')->nullable();
            $table->unsignedInteger('factor_seguridad')->nullable();
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
        Schema::dropIfExists('factores_seguridads');
    }
}
