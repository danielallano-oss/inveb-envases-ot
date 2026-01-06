<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class SecuenciaOperacional extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'secuencias_operacionales';

    protected $fillable = ['id', 'codigo', 'descripcion', 'nombre_corto','planta_id','active','deleted'];

    public function planta()
    {
        return $this->belongsTo(Planta::class, "planta_id");
    }
}
