<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstallationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 255)->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('tipo_pallet')->nullable();
            $table->unsignedInteger('altura_pallet')->nullable();
            $table->unsignedInteger('sobresalir_carga')->nullable();
            $table->unsignedInteger('bulto_zunchado')->nullable();
            $table->unsignedInteger('formato_etiqueta')->nullable();
            $table->unsignedInteger('etiquetas_pallet')->nullable();
            $table->unsignedInteger('termocontraible')->nullable();
            $table->unsignedInteger('fsc')->nullable();
            $table->unsignedInteger('pais_mercado_destino')->nullable();
            $table->unsignedInteger('certificado_calidad')->nullable();
            $table->string('nombre_contacto', 255)->nullable();
            $table->string('cargo_contacto', 255)->nullable();
            $table->string('email_contacto', 255)->nullable();
            $table->string('phone_contacto', 255)->nullable();
            $table->string('direccion_contacto', 255)->nullable();
            $table->unsignedInteger('comuna_contacto')->nullable();
            $table->enum('active_contacto',['activo','inactivo'])->default('inactivo');
            $table->string('nombre_contacto_2', 255)->nullable();
            $table->string('cargo_contacto_2', 255)->nullable();
            $table->string('email_contacto_2', 255)->nullable();
            $table->string('phone_contacto_2', 255)->nullable();
            $table->string('direccion_contacto_2', 255)->nullable();
            $table->unsignedInteger('comuna_contacto_2')->nullable();
            $table->enum('active_contacto_2',['activo','inactivo'])->default('inactivo');
            $table->string('nombre_contacto_3', 255)->nullable();
            $table->string('cargo_contacto_3', 255)->nullable();
            $table->string('email_contacto_3', 255)->nullable();
            $table->string('phone_contacto_3', 255)->nullable();
            $table->string('direccion_contacto_3', 255)->nullable();
            $table->unsignedInteger('comuna_contacto_3')->nullable();
            $table->enum('active_contacto_3',['activo','inactivo'])->default('inactivo');
            $table->string('nombre_contacto_4', 255)->nullable();
            $table->string('cargo_contacto_4', 255)->nullable();
            $table->string('email_contacto_4', 255)->nullable();
            $table->string('phone_contacto_4', 255)->nullable();
            $table->string('direccion_contacto_4', 255)->nullable();
            $table->unsignedInteger('comuna_contacto_4')->nullable();
            $table->enum('active_contacto_4',['activo','inactivo'])->default('inactivo');
            $table->string('nombre_contacto_5', 255)->nullable();
            $table->string('cargo_contacto_5', 255)->nullable();
            $table->string('email_contacto_5', 255)->nullable();
            $table->string('phone_contacto_5', 255)->nullable();
            $table->string('direccion_contacto_5', 255)->nullable();
            $table->unsignedInteger('comuna_contacto_5')->nullable();
            $table->enum('active_contacto_5',['activo','inactivo'])->default('inactivo');
            $table->tinyInteger('deleted')->default(0);
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
        Schema::dropIfExists('installations');
    }
}
