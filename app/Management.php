<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class Management extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public $table = 'managements';
    public function type()
    {
        return $this->belongsTo(ManagementType::class, 'management_type_id');
    }
    public function ot()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(States::class);
    }

    public function area()
    {
        return $this->belongsTo(WorkSpace::class, 'work_space_id');
    }

    public function area_consultada()
    {
        return $this->belongsTo(WorkSpace::class, 'consulted_work_space_id');
    }
    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function respuesta()
    {
        return $this->hasOne(Answer::class, 'management_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
}
