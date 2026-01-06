<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class PrintingMachine extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'printing_machines'; 

}
