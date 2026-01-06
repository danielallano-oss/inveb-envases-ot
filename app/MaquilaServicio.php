<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class MaquilaServicio extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
}
