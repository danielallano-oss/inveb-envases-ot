<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Impresion extends Model implements Auditable
{   
    use \OwenIt\Auditing\Auditable;

    protected $table = 'impresion'; 
 
}