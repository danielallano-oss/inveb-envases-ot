<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cardboard extends Model
{
    public function mermas_corrugadora()
    {
        return $this->hasMany(MermaCorrugadora::class, 'carton_id', 'id');
    }

    public function tapa_interior()
    {
        return $this->hasOne(Paper::class, "codigo", "codigo_tapa_interior");
    }

    public function primera_onda()
    {
        return $this->hasOne(Paper::class, "codigo", "codigo_onda_1");
    }

    public function tapa_media()
    {
        return $this->hasOne(Paper::class, "codigo", "codigo_tapa_media");
    }

    public function segunda_onda()
    {
        return $this->hasOne(Paper::class, "codigo", "codigo_onda_2");
    }

    public function tapa_exterior()
    {
        return $this->hasOne(Paper::class, "codigo", "codigo_tapa_exterior");
    }
}
