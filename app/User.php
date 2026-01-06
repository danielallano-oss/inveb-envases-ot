<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    use HasApiTokens, Notifiable;    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $appends = array('fullname');
    protected $guarded = [];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_verified_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    


    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    public function getFullNameAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function ots()
    {
        return $this->belongsToMany(WorkOrder::class, 'user_work_orders');
    }
    public function otsCreadas()
    {
        return $this->hasMany(WorkOrder::class, 'creador_id');
    }

    public function desarrollosCompletados()
    {
        return $this->hasMany(WorkOrder::class, 'creador_id')->whereIn('tipo_solicitud', [1, 5])->join('managements', 'work_orders.id', 'managements.work_order_id')
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

    public function totalNotificacionesActivas()
    {
        return $this->hasMany(Notification::class, 'user_id')->where("active", 1);
    }

    public function asignacion()
    {
        return $this->hasMany(UserWorkOrder::class);
    }

    // VERIFICAR ROLES
    // Verifica el rol enviado
    public function hasRole($role)
    {
        if ($this->role()->where('nombre', $role)->first()) {
            return true;
        }
        return false;
    }

    // Verifica arreglo de roles 
    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }
        return false;
    }

    public function isAdmin()
    {
        return Auth()->user()->role_id == Constants::Admin;
    }

    public function isGerenteGeneral()
    {
        return Auth()->user()->role_id == Constants::Gerente;
    }

    public function isGerenteComercial()
    {
        return Auth()->user()->role_id == Constants::GerenteComercial;
    }

    public function isJefeVenta()
    {
        return Auth()->user()->role_id == Constants::JefeVenta;
    }

    public function isVendedor()
    {
        return Auth()->user()->role_id == Constants::Vendedor;
    }

    public function isJefeDesarrollo()
    {
        return Auth()->user()->role_id == Constants::JefeDesarrollo;
    }

    public function isIngeniero()
    {
        return Auth()->user()->role_id == Constants::Ingeniero;
    }

    public function isJefeDiseño()
    {
        return Auth()->user()->role_id == Constants::JefeDiseño;
    }

    public function isDiseñador()
    {
        return Auth()->user()->role_id == Constants::Diseñador;
    }

    public function isJefePrecatalogador()
    {
        return Auth()->user()->role_id == Constants::JefePrecatalogador;
    }

    public function isPrecatalogador()
    {
        return Auth()->user()->role_id == Constants::Precatalogador;
    }

    public function isJefeCatalogador()
    {
        return Auth()->user()->role_id == Constants::JefeCatalogador;
    }

    public function isCatalogador()
    {
        return Auth()->user()->role_id == Constants::Catalogador;
    }

    public function isJefeMuestras()
    {
        return Auth()->user()->role_id == Constants::JefeMuestras;
    }

    public function isTecnicoMuestras()
    {
        return Auth()->user()->role_id == Constants::TecnicoMuestras;
    }

    public function isAPI()
    {
        return Auth()->user()->role_id == Constants::API;
    }

    public function isSuperAdministrador()
    {
        return Auth()->user()->role_id == Constants::SuperAdministrador;
    }
    
    // Funciones multusuario
    public function isJefeCotizador()
    {
        return Auth()->user()->role_id == Constants::Gerente ||
            Auth()->user()->role_id == Constants::GerenteComercial ||
            Auth()->user()->role_id == Constants::JefeVenta;
    }

    public function salacorte()
    {
        return $this->belongsTo(SalaCorte::class);
    }

    public function isVendedorExterno()
    {
        return Auth()->user()->role_id == Constants::VendedorExterno;
    }
}
