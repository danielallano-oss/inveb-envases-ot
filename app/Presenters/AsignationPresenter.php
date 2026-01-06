<?php

namespace App\Presenters;

use App\UserWorkOrder;

class Asignation
{
    protected $asignation;

    function __construct(UserWorkOrder $asignation)
    {
        $this->asignation = $asignation;
    }
}
