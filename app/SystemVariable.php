<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class SystemVariable extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'system_variables';

    protected $fillable = ['id', 'name', 'contents'];
}
