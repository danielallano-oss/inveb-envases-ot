<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public function ot()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
    public function generador()
    {
        return $this->belongsTo(User::class, 'generador_id');
    }
}
