<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Mercado extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];
}
