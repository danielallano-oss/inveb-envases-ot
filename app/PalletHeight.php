<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class PalletHeight extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'pallet_height'; 

}
