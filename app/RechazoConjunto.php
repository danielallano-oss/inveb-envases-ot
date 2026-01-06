<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class RechazoConjunto extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'rechazo_conjunto';

    protected $fillable = ['id', 'proceso_id', 'porcentaje_proceso_solo', 'porcentaje_proceso_barniz', 'porcentaje_proceso_maquila','active'];

    public function proceso()
    {
        return $this->belongsTo(Process::class, "proceso_id");
    }
}
