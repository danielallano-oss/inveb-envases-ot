<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Fsc extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'fsc'; 

    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}
