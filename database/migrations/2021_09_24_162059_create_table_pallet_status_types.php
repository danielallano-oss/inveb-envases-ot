<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePalletStatusTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pallet_status_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        DB::table('pallet_status_types')->insert([
            ['descripcion' => 'Por Definir' , 'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Definido Por Cliente' ,'created_at' => NOW(), 'updated_at' => NOW()],
            ['descripcion' => 'Palletizado Con V"B"' , 'created_at' => NOW(), 'updated_at' => NOW()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pallet_status_types');
    }
}
