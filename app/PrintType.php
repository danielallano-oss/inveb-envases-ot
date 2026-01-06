<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class PrintType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'print_type'; 

    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}
