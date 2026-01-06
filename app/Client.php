<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Client extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }
    public function ots()
    {
        return $this->hasMany(WorkOrder::class, "client_id");
    }
    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, "client_id");
    }
    public function getNombreSapAttribute()
    {
        return $this->nombre . " - " . $this->codigo;
    }

    public function desarrollosCompletados()
    {
        return $this->hasMany(WorkOrder::class, 'client_id')->whereIn('tipo_solicitud', [1, 5])->join('managements', 'work_orders.id', 'managements.work_order_id')
            ->where('managements.management_type_id', 1)
            ->whereIn("managements.state_id", [8]) // 8 = Terminados
            ->where('managements.id', function ($q) {
                $q->select('id')
                    ->from('managements')
                    ->whereColumn('work_order_id', 'work_orders.id')
                    ->where('managements.management_type_id', 1)
                    ->latest()
                    ->limit(1);
            });
    }

    public function ClasificacionCliente()
    {
        return $this->belongsTo(ClasificacionCliente::class, "clasificacion");
    }
}
