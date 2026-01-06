<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class CoverageInternal extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'coverage_internal'; 
 
}
