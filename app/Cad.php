<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Cad extends Model  implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }

    public function detalleCotizacion()
    {
        return $this->hasMany(DetalleCotizacion::class, "cad_material_id");
    }
}
