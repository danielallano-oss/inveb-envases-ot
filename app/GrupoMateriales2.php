<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class GrupoMateriales2 extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'grupo_materiales_2';

    protected $fillable = ['id', 'pruduct_type_id', 'codigo','active'];

    public function tipo_producto()
    {
        return $this->belongsTo(ProductType::class, "pruduct_type_id");
    }
}
