<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserWorkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_work_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('work_order_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('area_id');
            $table->bigInteger('tiempo_inicial');
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
        Schema::dropIfExists('user_work_orders');
    }
}
