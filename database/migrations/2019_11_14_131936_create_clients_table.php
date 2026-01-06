<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rut', 20)->unique()->nullable();
            $table->string('nombre', 100);
            $table->string('codigo', 100)->nullable();
            $table->dateTime('fecha_ingreso')->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('poblacion', 200)->nullable();
            $table->string('codigo_zona', 200)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('nacional')->default(1);
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
        Schema::dropIfExists('clients');
    }
}
