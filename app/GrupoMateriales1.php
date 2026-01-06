<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class GrupoMateriales1 extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'grupo_materiales_1';

    protected $fillable = ['id', 'armado_id', 'codigo','active'];

    public function armado()
    {
        return $this->belongsTo(Armado::class, "armado_id");
    }
}
