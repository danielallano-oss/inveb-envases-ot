<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCotizacionApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizacion_approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('motivo', 191)->nullable();
            $table->unsignedInteger('role_do_action');
            $table->string('action_made');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('cotizacion_id');
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
        Schema::dropIfExists('cotizacion_approvals');
    }
}
