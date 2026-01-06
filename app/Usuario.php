<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Usuario extends Model
{
  use Notifiable;

  /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'sac';

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'users';
}
