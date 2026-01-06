<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalCharacteristicsType extends Model
{
    protected $table = 'additional_characteristics_type'; 

    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}