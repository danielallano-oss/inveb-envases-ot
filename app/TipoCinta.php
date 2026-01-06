<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class TipoCinta extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'tipos_cintas';

    protected $fillable = ['id', 'descripcion','codigo','active'];
}
