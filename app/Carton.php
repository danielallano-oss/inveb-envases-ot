<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Carton extends Model implements Auditable
{
    //
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];

    public function materials()
    {
        $this->hasMany(Material::class);
    }
    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }

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
    public function onda_powerplay()
    {
        return $this->hasOne(Paper::class, "codigo", "codigo_onda_1_2");
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

    public function getOnda1Attribute()
    {
        if ($this->onda == "P-BC") {
            return "B";
        }
        if ($this->onda == "P") {
            return "C";
        }
        // retornamos la onda
        return substr($this->onda, 0, 1);
    }

    public function getOnda2Attribute()
    {
        if ($this->onda == "P-BC") {
            return "C";
        }
        if (strlen($this->onda) < 2) {
            return null;
        }
        // retornamos la onda
        return substr($this->onda, 1, 1);
    }
}
