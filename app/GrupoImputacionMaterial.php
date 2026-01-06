<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class GrupoImputacionMaterial extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'grupo_imputacion_materiales';

    protected $fillable = ['id', 'proceso', 'codigo','familia','material_modelo','active'];

    // public function proceso()
    // {
    //     return $this->belongsTo(Process::class, "proceso_id");
    // }
}
