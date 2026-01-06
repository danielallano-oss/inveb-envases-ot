<?php

namespace App\Presenters;

use App\User;

class UserPresenter
{
    protected $user;

    function __construct(User $user)
    {
        $this->user = $user;
    }
}
