<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'proveedores';

    protected $fillable = ['id', 'name'];
}
