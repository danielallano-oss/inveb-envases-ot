<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class ManagementType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public function managements()
    {
        return $this->hasMany(Management::class);
    }
}
