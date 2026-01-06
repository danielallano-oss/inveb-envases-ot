<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class FoodType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'food_types';

    protected $fillable = ['id', 'descripcion'];
}
