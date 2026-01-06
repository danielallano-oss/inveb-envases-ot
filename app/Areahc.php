<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Areahc extends Model
{
    public function onda()
    {
        return $this->belongsTo(TipoOnda::class, "onda_id");
    }
}
