<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class PorcentajeMargen extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'porcentajes_margenes'; 

    protected $fillable = [ 'id', 
                            'rubro_id', 
                            'clasificacion_cliente_id', 
                            'bruto_esperado', 
                            'servir_esperado', 
                            'ebitda_esperado', 
                            'activo'];

    public function rubro()
    {
        return $this->hasOne(Rubro::class,'id','rubro_id');
    }
    
    public function clasificacion()
    {
        return $this->hasOne(ClasificacionCliente::class,'id','clasificacion_cliente_id');
    }    
    
}
