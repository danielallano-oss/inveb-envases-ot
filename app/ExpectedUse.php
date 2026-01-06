<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class ExpectedUse extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'expected_use';

    protected $fillable = ['id', 'descripcion'];
}
