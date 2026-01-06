<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Trazabilidad extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'trazabilidad';

}
