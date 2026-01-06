<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Zuncho extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'zunchos';

    protected $fillable = ['id', 'descripcion'];
}
