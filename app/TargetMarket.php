<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class TargetMarket extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'target_market';

    protected $fillable = ['id', 'descripcion'];
}
