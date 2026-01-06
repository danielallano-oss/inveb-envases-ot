<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class File extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public function management()
    {
        return $this->belongsTo(Management::class);
    }
}
