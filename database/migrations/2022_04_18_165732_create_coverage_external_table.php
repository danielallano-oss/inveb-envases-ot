<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoverageExternalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coverage_external', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        DB::table('coverage_external')->insert([
            ['descripcion' => 'No aplica' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Barniz hidrorepelente' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Barniz acuoso' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Barniz UV' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Cera' , 'created_at' => NOW(), 'updated_at' => NOW()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coverage_external');
    }
}
