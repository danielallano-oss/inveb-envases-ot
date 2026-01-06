<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class TipoOnda extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public function areahc()
    {
        return $this->hasMany(Areahc::class);
    }
}
