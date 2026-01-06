<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BitacoraWorkOrder extends Model
{
    public $table = 'bitacora_work_orders';

    public function ot()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getDatosModificadosAttribute($value)
    {
        return json_decode($value, true);
    }
    public function getUserDataAttribute($value)
    {
        return json_decode($value, true);
    }

}
