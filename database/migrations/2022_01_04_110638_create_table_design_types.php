<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDesignTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('design_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('descripcion');
            $table->tinyInteger('tipo');
            $table->string('complejidad');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        DB::table('design_types')->insert([
            [   'descripcion' => 'Sin impresión o solo cambio de cartón de una referencia (conservando color de papel tapa exterior y tipo de onda del cartón).', 
                'tipo' => '1',
                'complejidad' => 'Baja',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [   'descripcion' => 'Diseño de 1 a 4 colores con antecedentes gráficos editables en texto y reubicación o incorporación de elementos sin vectorizar. Adaptación como mejora productiva e EEII.', 
                'tipo' => '2',
                'complejidad' => 'Baja',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [   'descripcion' => 'Idem tipo 2, pero con vectorización adicional de figuras y/o solicitud de estires de tinta.', 
                'tipo' => '3',
                'complejidad' => 'Media',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [   'descripcion' => 'Adaptación a flexo cartón desde otro sistma de impresión, propuesta reduccción de colores y/o trabajo con prueba de color digital. Propuestas de diseño nuevo o inédito. Manipulación de imágenes fotográficas. Certificación FSC.', 
                'tipo' => '4',
                'complejidad' => 'Alta',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [   'descripcion' => 'Cajas offset o alt gráfica.', 
                'tipo' => '5',
                'complejidad' => 'Alta',
                'created_at' => NOW(), 
                'updated_at' => NOW()
            ],
            [   'descripcion' => 'Cajas tiro y retiro.', 
                'tipo' => '6',
                'complejidad' => 'Alta',
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
        Schema::dropIfExists('design_types');
    }
}
