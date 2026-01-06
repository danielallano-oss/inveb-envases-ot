<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class UserWorkOrder extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ot()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function present()
    {
        return new AsignationPresenter($this);
    }
}
