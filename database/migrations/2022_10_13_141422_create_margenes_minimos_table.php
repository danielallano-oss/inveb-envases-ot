<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMargenesMinimosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('margenes_minimos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('rubro_id')->nullable();
            $table->string('rubro_descripcion')->nullable();
            $table->unsignedInteger('mercado_id')->nullable();
            $table->string('mercado_descripcion')->nullable();
            $table->char('cluster')->nullable();
            $table->integer('minimo')->nullable();
            $table->integer('activo')->default(1);
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
        Schema::dropIfExists('margenes_minimos');
    }
}
