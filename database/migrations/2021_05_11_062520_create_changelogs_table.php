<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChangelogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('changelogs', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('column_name')->nullable();
            $table->string('table_name')->nullable();
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->unsignedInteger('item_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->text('user');
            $table->string('codigo_operacion');
            $table->string('tipo_operacion');
            $table->longText('excel_row');

            // id,
            //  nombre campo,
            //   valor anterior,
            //  valor actual,
            //  item_id,
            //  tabla donde esta el dato,
            // user_id usuario que la cambio,
            //  user (user model),
            //  fecha del cambio,
            //  operacion (Insert o Update),
            //  Codigo Operacion (timestamp de subida del archivo + userid),
            //  Guardar linea excel (longtext de la fila)
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
        Schema::dropIfExists('changelogs');
    }
}
