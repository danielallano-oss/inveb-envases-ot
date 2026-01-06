<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cad');
            // Medidas exteriores
            $table->integer('externo_largo')->default(0);
            $table->integer('externo_ancho')->default(0);
            $table->integer('externo_alto')->default(0);
            // Medidas interiores
            $table->integer('interno_largo')->default(0);
            $table->integer('interno_ancho')->default(0);
            $table->integer('interno_alto')->default(0);
            $table->decimal('area_producto', 10, 2)->default(0);
            $table->integer('largura_hm')->default(0);
            $table->integer('anchura_hm')->default(0);
            $table->integer('largura_hc')->default(0);
            $table->integer('anchura_hc')->default(0);
            $table->integer('area_hm')->default(0);
            $table->integer('area_hc_unitario')->default(0);
            $table->decimal('rayado_c1r1', 5, 1)->default(0);
            $table->decimal('rayado_r1_r2', 5, 1)->default(0);
            $table->decimal('rayado_r2_c2', 5, 1)->default(0);
            $table->decimal('recorte_caracteristico', 5, 4)->default(0);
            $table->decimal('recorte_adicional', 5, 4)->default(0);
            $table->integer('veces_item')->default(0);
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
        Schema::dropIfExists('cads');
    }
}
