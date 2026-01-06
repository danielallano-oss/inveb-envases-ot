<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function area()
    {
        return $this->belongsTo(WorkSpace::class, 'work_space_id');
    }
}
