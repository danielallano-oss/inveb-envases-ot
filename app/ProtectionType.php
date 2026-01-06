<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class ProtectionType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'protection_type'; 

    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}