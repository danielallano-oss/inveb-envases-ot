<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Envase extends Model
{
    //

    protected $guarded = [];
    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}
