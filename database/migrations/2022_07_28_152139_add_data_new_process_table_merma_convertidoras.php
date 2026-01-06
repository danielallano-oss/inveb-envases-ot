<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataNewProcessTableMermaConvertidoras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::table('merma_convertidoras')->insert([
            //PROCESO DIECUTTER - ALTA GRÁFICA
            //Planta Buin
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '1' ,
                'porcentaje_merma_convertidora' => '0.059639069',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '2' ,
                'porcentaje_merma_convertidora' => '0.062200259',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '3' ,
                'porcentaje_merma_convertidora' => '0.040454006',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '4' ,
                'porcentaje_merma_convertidora' => '0.062429835',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '6' ,
                'porcentaje_merma_convertidora' => '0.055124582',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '7' ,
                'porcentaje_merma_convertidora' => '0.045734951',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '8' ,
                'porcentaje_merma_convertidora' => '0.05114901',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '9' ,
                'porcentaje_merma_convertidora' => '0.073372121',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '10' ,
                'porcentaje_merma_convertidora' => '0.050790477',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '11' ,
                'porcentaje_merma_convertidora' => '0.04821842',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '12' ,
                'porcentaje_merma_convertidora' => '0.100438717',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '13' ,
                'porcentaje_merma_convertidora' => '0.061154064',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '14' ,
                'porcentaje_merma_convertidora' => '0.263146312',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '15' ,
                'porcentaje_merma_convertidora' => '0.053307851',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '16' ,
                'porcentaje_merma_convertidora' => '0.069698011',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '17' ,
                'porcentaje_merma_convertidora' => '0.060858645',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '18' ,
                'porcentaje_merma_convertidora' => '0.046235321',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '11' ,
                'rubro_id' => '19' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            //Planta tiltil
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '1' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '2' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '3' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '4' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '6' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '7' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '8' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '9' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '10' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '11' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '12' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '13' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '14' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '15' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '16' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '17' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '18' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '11' ,
                'rubro_id' => '19' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            //Planta Osorno
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '1' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '2' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '3' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '4' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '6' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '7' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '8' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '9' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '10' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '11' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '12' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '13' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '14' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '15' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '16' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '17' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '18' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '11' ,
                'rubro_id' => '19' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],


            //PROCESO DIECUTTER -C/PEGADO ALTA GRÁFICA
            //Planta Buin
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '1' ,
                'porcentaje_merma_convertidora' => '0.018442131',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '2' ,
                'porcentaje_merma_convertidora' => '0.06724139',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '3' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '4' ,
                'porcentaje_merma_convertidora' => '0.043136532',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '6' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '7' ,
                'porcentaje_merma_convertidora' => '0.117610011',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '8' ,
                'porcentaje_merma_convertidora' => '0.067405364',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '9' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '10' ,
                'porcentaje_merma_convertidora' => '0.071373934',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '11' ,
                'porcentaje_merma_convertidora' => '0.084896282',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '12' ,
                'porcentaje_merma_convertidora' => '0.086137988',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '13' ,
                'porcentaje_merma_convertidora' => '0.08135306',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '14' ,
                'porcentaje_merma_convertidora' => '0.072094073',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '15' ,
                'porcentaje_merma_convertidora' => '0.061030318',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '16' ,
                'porcentaje_merma_convertidora' => '0.12931099',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '17' ,
                'porcentaje_merma_convertidora' => '0.049876556',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '18' ,
                'porcentaje_merma_convertidora' => '0.108793746',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '1' , 
                'process_id' => '12' ,
                'rubro_id' => '19' ,
                'porcentaje_merma_convertidora' => '0.076663964',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            //Planta tiltil
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '1' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '2' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '3' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '4' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '6' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '7' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '8' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '9' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '10' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '11' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '12' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '13' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '14' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '15' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '16' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '17' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '18' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '2' , 
                'process_id' => '12' ,
                'rubro_id' => '19' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            //Planta Osorno
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '1' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '2' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '3' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '4' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '6' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '7' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '8' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '9' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '10' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '11' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '12' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '13' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '14' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
                ]
            ,
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '15' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '16' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '17' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '18' ,
                'porcentaje_merma_convertidora' => '0',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [
                'planta_id' => '3' , 
                'process_id' => '12' ,
                'rubro_id' => '19' ,
                'porcentaje_merma_convertidora' => '0',
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
        //
    }
}
