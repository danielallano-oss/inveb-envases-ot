<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class CantidadBase extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'cantidad_base';

    protected $fillable = ['id', 'proceso_id', 'cantidad_buin', 'cantidad_tiltil', 'cantidad_osorno','active'];

    public function proceso()
    {
        return $this->belongsTo(Process::class, "proceso_id");
    }
}
