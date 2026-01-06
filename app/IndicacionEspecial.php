<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class IndicacionEspecial extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
     
    protected $table = 'indicaciones_especiales';     
 
}