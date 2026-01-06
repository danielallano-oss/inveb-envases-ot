<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class TransportationWay extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'transportation_way';

    protected $fillable = ['id', 'descripcion'];
}
