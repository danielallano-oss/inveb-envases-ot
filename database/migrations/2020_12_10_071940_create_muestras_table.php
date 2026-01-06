<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMuestrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('muestras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger("work_order_id");
            $table->unsignedInteger("user_id");
            $table->string("cad")->nullable();
            $table->unsignedInteger("cad_id")->nullable();
            $table->unsignedInteger("carton_id")->nullable();
            $table->unsignedInteger("pegado_id")->nullable();
            $table->string("tiempo_unitario", 191)->nullable();
            $table->date("fecha_corte")->nullable();
            $table->text("destinatarios_id");
            $table->unsignedInteger("cantidad_vendedor")->nullable();
            $table->string("comentario_vendedor", 191)->nullable();
            $table->unsignedInteger("cantidad_diseñador")->nullable();
            $table->string("comentario_diseñador", 191)->nullable();
            $table->unsignedInteger("cantidad_laboratorio")->nullable();
            $table->string("comentario_laboratorio", 191)->nullable();
            // Destinos
            $table->string("destinatario_1", 191)->nullable();
            $table->unsignedInteger("comuna_1")->nullable();
            $table->string("direccion_1", 191)->nullable();
            $table->unsignedInteger("cantidad_1")->nullable();
            $table->string("comentario_1", 191)->nullable();

            $table->string("destinatario_2", 191)->nullable();
            $table->unsignedInteger("comuna_2")->nullable();
            $table->string("direccion_2", 191)->nullable();
            $table->unsignedInteger("cantidad_2")->nullable();
            $table->string("comentario_2", 191)->nullable();

            $table->string("destinatario_3", 191)->nullable();
            $table->unsignedInteger("comuna_3")->nullable();
            $table->string("direccion_3", 191)->nullable();
            $table->unsignedInteger("cantidad_3")->nullable();
            $table->string("comentario_3", 191)->nullable();

            $table->string("destinatario_4", 191)->nullable();
            $table->unsignedInteger("comuna_4")->nullable();
            $table->string("direccion_4", 191)->nullable();
            $table->unsignedInteger("cantidad_4")->nullable();
            $table->string("comentario_4", 191)->nullable();
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
        Schema::dropIfExists('muestras');
    }
}
