<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sectores';

    protected $guarded = [];


    public function product_type()
    {
        return $this->belongsTo(ProductType::class);
    }
}
