<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Cotizacion extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;

    // Funcion para cargar todas las relaciones en un call
    public function scopeWithAll($query)
    {
        $query->with(
            'detalles.rubro.mermas_convertidora',
            'detalles.subsubhierarchy.subhierarchy.hierarchy',
            'detalles.users',
            'detalles.carton.mermas_corrugadora',
            'detalles.carton.tapa_interior',
            'detalles.carton.tapa_media',
            'detalles.carton.tapa_exterior',
            'detalles.carton.primera_onda',
            'detalles.carton.segunda_onda',
            'detalles.carton_esquinero.papel_1',
            'detalles.carton_esquinero.papel_2',
            'detalles.carton_esquinero.papel_3',
            'detalles.carton_esquinero.papel_4',
            'detalles.carton_esquinero.papel_5',
            'detalles.productType',
            'detalles.proceso',
            'detalles.planta.factores_onda',
            'detalles.planta.consumos_adhesivo',
            'detalles.planta.consumos_energia',
            'detalles.variables_cotizador',
            'detalles.flete',
            'detalles.detalles_hermanos',
            'detalles.cotizacion',
            'versiones',
            'parent',
            'user',
            "detalles_ganados",
            "detalles_perdidos",
            "detalles_marcados",
        );
    }
    // public function previous()
    // {
    //     return $this->belongsTo(Cotizacion::class, 'previous_version_id')->with('previous');
    // }
    public function versiones()
    {

        return $this->hasMany(Cotizacion::class, "original_version_id", "original_version_id")->orderBy('id', 'desc');
    }
    public function parent()
    {
        return $this->belongsTo(Cotizacion::class, "original_version_id");
    }
    public function detalles()
    {
        return $this->hasMany(DetalleCotizacion::class);
    }
    public function detalles_ganados()
    {
        return $this->hasMany(DetalleCotizacion::class)->where('estado', 1);
    }
    public function detalles_perdidos()
    {
        return $this->hasMany(DetalleCotizacion::class)->where('estado', 2);
    }
    public function detalles_marcados()
    {
        return $this->hasMany(DetalleCotizacion::class)->whereNotNull('estado');
    }
    public function aprobaciones()
    {
        return $this->hasMany(CotizacionApproval::class)->orderBy('id', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function estado()
    {
        return $this->belongsTo(CotizacionEstado::class, "estado_id");
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            // ... code here
        });

        self::created(function ($model) {
            // ... code here
        });

        self::updating(function ($model) {
            if ($model->isDirty('estado_ids')) {
                // dd($model->getChanges());
                // dd($model->estado_id);
                switch ($model->id) {
                    case 1:
                        // Si cambia a estado borrador la cotizacion tomara valores de mantenedor
                        break;
                    case 2:
                        // Si cambia a estado por Aprobar la cotizacion debe guardar todos los valores de mantenedores
                        break;
                    default:
                        # code...
                        break;
                }
            }
            // dd($model);
        });

        self::updated(function ($model) {
            if ($model->isDirty()) {

                // dd($model->getChanges());
                // dd($model);
            }
        });

        self::deleting(function ($model) {
            // ... code here
        });

        self::deleted(function ($model) {
            // ... code here
        });
    }
}
