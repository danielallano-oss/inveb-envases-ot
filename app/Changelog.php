<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Changelog extends Model
{

    protected $guarded = [];

    protected $casts = [
        'user' => 'array', // Will convert to (Array)
        'excel_row' => 'array', // Will convert to (Array)
    ];
}
