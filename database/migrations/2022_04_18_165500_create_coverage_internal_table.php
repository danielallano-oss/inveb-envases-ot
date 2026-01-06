<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoverageInternalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coverage_internal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        DB::table('coverage_internal')->insert([
            ['descripcion' => 'No aplica' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Barniz hidrorepelente' , 'created_at' => NOW(), 'updated_at' => NOW()],
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
        Schema::dropIfExists('coverage_internal');
    }
}
