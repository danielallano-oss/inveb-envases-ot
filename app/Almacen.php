<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'almacenes';

    protected $fillable = ['id', 'codigo', 'denominacion', 'centro','active'];
}
