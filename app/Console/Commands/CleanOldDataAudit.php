<?php

namespace App\Console\Commands;

use App\Audit;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Hash;

class CleanOldDataAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CleanOldDataAuditCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Esta tarea permite limpiar los logs de auditoria registrados mayores a 120 dias de antiguedad';

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
      $date_now   = date('Y-m-d 00:00:00');
      $date_past  = strtotime('-120 day', strtotime($date_now));
      $date_past  = date('Y-m-d 00:00:00', $date_past);
      
      $old_data_audit_delete = Audit::where('created_at','<',$date_past)->delete();
      
    }
}
