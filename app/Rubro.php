<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Rubro extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public function subsubhierarchies()
    {
        return $this->hasMany(Subsubhierarchy::class);
    }

    public function mermas_convertidora()
    {
        return $this->hasMany(MermaConvertidora::class, 'rubro_id', 'id');
    }
}
