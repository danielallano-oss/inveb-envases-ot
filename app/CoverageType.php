<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class CoverageType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'coverage_types'; 

    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}
