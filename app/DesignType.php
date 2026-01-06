<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class DesignType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'design_types'; 

    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}
