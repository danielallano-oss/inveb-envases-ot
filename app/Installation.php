<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Installation extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'installations'; 

    public function TipoPallet()
    {
        return $this->hasOne(PalletType::class,'id','tipo_pallet');
    }

    public function Fsc()
    {
        return $this->hasOne(Fsc::class,'codigo','fsc');
    }

    public function formato_etiqueta_pallet()
    {
        return $this->hasOne(PalletTagFormat::class,'id',"formato_etiqueta");
    }
    public function qa()
    {
        return $this->hasOne(PalletQa::class,'id',"certificado_calidad");
    }

    public function TargetMarket()
    {
        return $this->hasOne(Pais::class,'id','pais_mercado_destino');
    }

    public function Comuna()
    {
        return $this->hasOne(CiudadesFlete::class,'id','comuna_contacto');
    }

    public function Comuna_2()
    {
        return $this->hasOne(CiudadesFlete::class,'id','comuna_contacto_2');
    }

    public function Comuna_3()
    {
        return $this->hasOne(CiudadesFlete::class,'id','comuna_contacto_3');
    }

    public function Comuna_4()
    {
        return $this->hasOne(CiudadesFlete::class,'id','comuna_contacto_4');
    }

    public function Comuna_5()
    {
        return $this->hasOne(CiudadesFlete::class,'id','comuna_contacto_5');
    }
 
}