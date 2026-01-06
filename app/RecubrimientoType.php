<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class RecubrimientoType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'recubrimiento_types'; 

    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}
