<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Muestra extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $guarded = [];
    // protected $dates = ['created_at', 'updated_at', 'fecha_corte'];
    protected $casts = [
        'destinatarios_id' => 'array', // Will convert to (Array)
    ];
    public function ot()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function cad_asignado()
    {
        return $this->belongsTo(Cad::class, "cad_id");
    }

    public function carton()
    {
        return $this->belongsTo(Carton::class);
    }
    public function carton_muestra()
    {
        return $this->belongsTo(Carton::class, "carton_muestra_id");
    }
    public function creador()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function ciudad_asignada()
    {
        return $this->belongsTo(CiudadesFlete::class, 'comuna_1');
    }

    public function planta_corte_vendedor()
    {
        return $this->belongsTo(SalaCorte::class,"sala_corte_vendedor");
    }
    public function planta_corte_diseñador()
    {
        return $this->belongsTo(SalaCorte::class,"sala_corte_diseñador");
    }
    public function planta_corte_laboratorio()
    {
        return $this->belongsTo(SalaCorte::class,"sala_corte_laboratorio");
    }
    public function planta_corte_1()
    {
        return $this->belongsTo(SalaCorte::class,"sala_corte_1");
    }
    public function planta_corte_2()
    {
        return $this->belongsTo(SalaCorte::class,"sala_corte_2");
    }
    public function planta_corte_3()
    {
        return $this->belongsTo(SalaCorte::class,"sala_corte_3");
    }
    public function planta_corte_4()
    {
        return $this->belongsTo(SalaCorte::class,"sala_corte_4");
    }

    public function planta_corte_diseñador_revision()
    {
        return $this->belongsTo(SalaCorte::class,"sala_corte_diseñador_revision");
    }
    
    // 
    // 
    // 
    // public function setFechaCorteAttribute($value)
    // {
    //     $this->attributes['fecha_corte'] = (new Carbon($value))->format('d/m/y');
    // }
}
