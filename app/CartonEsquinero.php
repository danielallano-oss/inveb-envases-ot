<?php

namespace App;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class CartonEsquinero extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    public function papel_1()
    {
        return $this->hasOne(Paper::class, "codigo", "codigo_papel_1");
    }
    public function papel_2()
    {
        return $this->hasOne(Paper::class, "codigo", "codigo_papel_2");
    }
    public function papel_3()
    {
        return $this->hasOne(Paper::class, "codigo", "codigo_papel_3");
    }
    public function papel_4()
    {
        return $this->hasOne(Paper::class, "codigo", "codigo_papel_4");
    }
    public function papel_5()
    {
        return $this->hasOne(Paper::class, "codigo", "codigo_papel_5");
    }
}
