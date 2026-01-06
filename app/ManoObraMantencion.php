<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class ManoObraMantencion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'mano_obra_mantencion';     

    protected $guarded = [];
    
    
}
