<?php

namespace App\Console\Commands;

use App\Audit;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Hash;

class UpdateUserDataAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateUserDataAuditCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Esta tarea permite actualizar la Data de los usuarios en la tabla audit';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     **/

    public function handle()
    {  
      $diccionario_users= [];
      $users=User::all();
      foreach ($users as $user) {
        $diccionario_users[$user->id]= array(
                                              'nombre'    => $user->nombre,
                                              'apellido'  => $user->apellido,
                                              'rut'       => $user->rut,
                                              'email'     => $user->email,
                                              'role_id'   => $user->role_id,
                                            );
      }

      $audits_data = Audit::whereNull('user_data')->get();
      foreach($audits_data as $audit_data){

        $data_user_update=$diccionario_users[$audit_data->user_id];

        $audit_user_data_update=Audit::where('id',$audit_data->id)
                                      ->whereNull('user_data')
                                      ->update(['user_data' => json_encode($data_user_update , JSON_UNESCAPED_UNICODE)]);
        
      }
      
     
      //$old_data_audit_delete = Audit::where('created_at','<',$date_past)->delete();
      
    }
}
