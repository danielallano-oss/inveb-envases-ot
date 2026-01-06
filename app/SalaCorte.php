<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class SalaCorte extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'salas_cortes';  
}