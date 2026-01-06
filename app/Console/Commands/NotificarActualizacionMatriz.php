<?php

namespace App\Console\Commands;

use App\Mail\NotificarActualizacionMatriz as AppNotificarActualizacionMatriz;
use App\Notification;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotificarActualizacionMatriz extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ActualizacionMatriz';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     */
    public function handle()
    {

        $users =User::where('role_id', 18)->where('active', 1)->get();

        foreach ($users as $user) {

            //fecha actual con formato dd/mm/YYYY
            $date_viernes = Carbon::now()->format('d/m/Y');
            $user->fecha = $date_viernes;

            //registor notificacion para superadmin
            $notificacion = new Notification();
            $notificacion->user_id = $user->id;
            $notificacion->generador_id = 1;
            $notificacion->work_order_id = 0;
            $notificacion->motivo = 'Recordatorio: Actualizar la base datos matrices al '.$date_viernes.'';
            $notificacion->observacion = 'Buen día, hoy es viernes '.$date_viernes.', recuerde que debe actualizar la base datos matrices, en el mantenedor del sistema. Saludos cordiales';
            $notificacion->save();

            Mail::to($user->email)->send(new AppNotificarActualizacionMatriz($user));

        }
    }
}
