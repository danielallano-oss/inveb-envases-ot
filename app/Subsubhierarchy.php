<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Subsubhierarchy extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $guarded = [];
    public function subhierarchy()
    {
        return $this->belongsTo(Subhierarchy::class);
    }
    public function rubro()
    {
        return $this->belongsTo(Rubro::class);
    }
    public function ots()
    {
        return  $this->hasMany(WorkOrder::class);
    }
}
