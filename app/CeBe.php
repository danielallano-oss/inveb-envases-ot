<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class CeBe extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'cebes';

    protected $fillable = ['id', 'planta_id', 'tipo', 'hierearchie_id','cebe','nombre_cebe','grupo_gastos_generales','active'];

    public function planta()
    {
        return $this->belongsTo(Planta::class, "planta_id");
    }

    public function mercados()
    {
        return $this->belongsTo(Hierarchy::class, "hierearchie_id");
    }
}
