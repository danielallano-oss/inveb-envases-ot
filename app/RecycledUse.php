<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class RecycledUse extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'recycled_use';

    protected $fillable = ['id', 'descripcion'];
}
