<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'paises'; 

    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}