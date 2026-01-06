<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class ConsumoEnergia extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public function planta()
    {
        return $this->belongsTo(Planta::class);
    }
    public function proceso()
    {
        return $this->belongsTo(Process::class, "process_id");
    }
}
