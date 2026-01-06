<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fletes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('costo_clp_pallet', 12, 4);
            $table->unsignedInteger('planta_id');
            $table->unsignedInteger('ciudad_id');
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
        Schema::dropIfExists('fletes');
    }
}
