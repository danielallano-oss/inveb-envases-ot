<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class GrupoPlanta extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'grupo_plantas';

    protected $fillable = ['id', 'planta_id', 'centro','num_almacen','cebe','active'];

    public function planta()
    {
        return $this->belongsTo(Planta::class, "planta_id");
    }
}
