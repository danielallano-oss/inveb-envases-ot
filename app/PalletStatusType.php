<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class PalletStatusType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'pallet_status_types'; 

    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}