<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FactoresSeguridad extends Model
{
    //
    public function rubro()
    {
        return $this->belongsTo(Rubro::class);
    }
    public function envase()
    {
        return $this->belongsTo(Envase::class, "envase_id");
    }
}
