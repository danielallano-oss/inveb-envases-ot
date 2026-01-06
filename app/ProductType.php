<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $guarded = [];

    public function sectors()
    {
        $this->hasMany(Sector::class);
    }

    public function materials()
    {
        $this->hasMany(Material::class);
    }
}
