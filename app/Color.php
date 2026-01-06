<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Color extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    
    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
    public function getColorAttribute()
    {
        return $this->codigo . " " . $this->descripcion;
    }
}
