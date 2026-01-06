<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class ProductTypeDeveloping extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'product_type_developing';

    protected $fillable = ['id', 'descripcion'];
}
