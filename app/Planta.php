<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Planta extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $casts = [
        'formatos_bobina_corrugadora' => 'array',
        'merma_convertidora' => 'array',
    ];
    public function merma_corrugadora()
    {
        return $this->hasMany(MermaCorrugadora::class);
    }

    public function factores_onda()
    {
        return $this->hasMany(FactoresOnda::class);
    }

    public function consumos_adhesivo()
    {
        return $this->hasMany(ConsumoAdhesivo::class);
    }
    public function consumos_energia()
    {
        return $this->hasMany(ConsumoEnergia::class);
    }
    public function consumos_adhesivo_pegado()
    {
        return $this->hasMany(ConsumoAdhesivoPegado::class);
    }
    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}
