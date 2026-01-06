<?php

namespace App;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class TiempoTratamiento extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'tiempo_tratamiento';

    protected $fillable = [
        'id',
        'proceso_id',
        'tiempo_buin',
        'tiempo_tiltil',
        'tiempo_osorno',
        'tiempo_buin_powerply',
        'tiempo_buin_cc_doble',
        'active'
    ];

    public function proceso()
    {
        return $this->belongsTo(Process::class, "proceso_id");
    }
}
