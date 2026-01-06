<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Material extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function cad()
    {
        return $this->belongsTo(Cad::class);
    }

    public function carton()
    {
        return $this->belongsTo(Carton::class);
    }

    public function product_type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function style()
    {
        return $this->belongsTo(Style::class);
    }

    public function rayado()
    {
        return $this->belongsTo(Rayado::class);
    }
}
