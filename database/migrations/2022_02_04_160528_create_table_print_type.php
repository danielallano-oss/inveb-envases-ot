<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePrintType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('descripcion');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        DB::table('print_type')->insert([
            [   'descripcion' => 'Solo delantera (0-8 colores, incluyendo barniz)', 
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [   'descripcion' => 'Delantera (0-5 colores) + Trasera (1 color)', 
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('print_type');
    }
}
