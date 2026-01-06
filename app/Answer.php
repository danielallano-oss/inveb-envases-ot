<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function gestion()
    {
        return $this->belongsTo(Management::class, 'management_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
