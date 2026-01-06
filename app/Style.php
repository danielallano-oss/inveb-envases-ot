<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Style extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    public function materials()
    {
        $this->hasMany(Material::class);
    }
    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}
