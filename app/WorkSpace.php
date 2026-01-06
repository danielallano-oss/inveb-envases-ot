<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class WorkSpace extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public function roles()
    {
        return $this->hasMany(Role::class);
    }
}
