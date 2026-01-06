<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class CotizacionEstado extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public function cotizacion()
    {
        return $this->hasMany(Cotizacion::class);
    }
}
