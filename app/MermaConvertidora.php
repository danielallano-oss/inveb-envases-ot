<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class MermaConvertidora extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    
    public function planta()
    {
        return $this->belongsTo(Planta::class);
    }


    public function proceso()
    {
        return $this->belongsTo(Process::class, "process_id");
    }

    public function rubro()
    {
        return $this->belongsTo(Rubro::class, "rubro_id");
    }
}
