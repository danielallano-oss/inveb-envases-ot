<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Adhesivo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'adhesivos';

    protected $fillable = ['id', 'planta_id', 'maquina', 'codigo', 'consumo','active'];


    public function Planta()
    {
        return $this->belongsTo(Planta::class, "planta_id");
    }
    
}
