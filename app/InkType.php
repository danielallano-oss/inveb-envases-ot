<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class InkType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'ink_types'; 
 
}