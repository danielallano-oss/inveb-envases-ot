<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class TipoBarniz extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'tipo_barniz';

    protected $fillable = ['id', 'descripcion'];
}
