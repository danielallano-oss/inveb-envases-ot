<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCotizacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizacions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('client_id');
            $table->string('nombre_contacto')->nullable();
            $table->string('email_contacto', 191)->nullable();
            $table->string('telefono_contacto', 12)->nullable()->comment('formato: 12 digitos sin espacios');
            $table->unsignedInteger("moneda_id")->nullable();
            $table->unsignedInteger("dias_pago")->nullable();
            // $table->unsignedInteger("margen")->default(0)->nullable();
            $table->unsignedInteger("comision")->default(0)->nullable();
            $table->string('observacion_interna')->nullable();
            $table->string('observacion_cliente')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger("estado_id")->default(1);
            $table->unsignedInteger('role_can_show')->nullable();
            $table->unsignedInteger('nivel_aprobacion')->nullable();
            $table->unsignedInteger('previous_version_id')->nullable();
            $table->unsignedInteger('original_version_id')->nullable();
            $table->unsignedInteger('version_number')->nullable();
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
        Schema::dropIfExists('cotizacions');
    }
}
