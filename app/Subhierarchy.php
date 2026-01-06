<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Subhierarchy extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    public function hierarchy()
    {
        return $this->belongsTo(Hierarchy::class);
    }

    public function subsubhierarchies()
    {
        return $this->hasMany(Subsubhierarchy::class);
    }
}
