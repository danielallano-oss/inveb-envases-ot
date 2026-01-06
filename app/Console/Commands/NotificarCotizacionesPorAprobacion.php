<?php

namespace App\Console\Commands;

use App\Cotizacion;
use App\Mail\NotificarCotizacionesPorAprobacion as AppNotificarCotizacionesPorAprobacion;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotificarCotizacionesPorAprobacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CotizacionesPorAprobacion';

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
        //    si hay cotizaciones en espera de aprobacion 
        $cotizacionesJefeVenta = Cotizacion::where('active', 1)->where("estado_id", 2)->where("role_can_show", 3)->get();
        $cotizacionesGerenteComercial = Cotizacion::where('active', 1)->where("estado_id", 2)->where("role_can_show", 15)->get();
        $cotizacionesGerenteGeneral = Cotizacion::where('active', 1)->where("estado_id", 2)->where("role_can_show", 2)->get();
        // roles
        // 3 = jefe venta 
        // 15= gerente comercial
        // 2= gerente general

        if (!$cotizacionesJefeVenta->isEmpty()) {
            $jefes = [];
            // Si no solo a los jefes de los creadores de dichas cotizaciones
            $cotizacionesJefeVenta->map(function ($cotizacion) use (&$jefes) {
                if ($cotizacion->user->jefe_id) {
                    $jefes[] = $cotizacion->user->jefe_id;
                }
            });

            $usuarios = User::whereIn('id', $jefes)->where('role_id', 3)->where('active', 1)->get();

            foreach ($usuarios as $user) {
                Mail::to($user->email)->bcc(['maria.botella@cmpc.com'])->send(new AppNotificarCotizacionesPorAprobacion($user));
                // Mail::to("gsanchez@invebchile.cl")->send(new AppNotificarCotizacionesPorAprobacion($user));
            }
        }
        if (!$cotizacionesGerenteComercial->isEmpty()) {
            $usuarios = User::where('role_id', 15)->where('active', 1)->get();
            foreach ($usuarios as $user) {
                Mail::to($user->email)->bcc(['maria.botella@cmpc.com'])->send(new AppNotificarCotizacionesPorAprobacion($user));
                // Mail::to("gsanchez@invebchile.cl")->send(new AppNotificarCotizacionesPorAprobacion($user));
            }
        }
        if (!$cotizacionesGerenteGeneral->isEmpty()) {
            $usuarios = User::where('role_id', 2)->where('active', 1)->get();
            foreach ($usuarios as $user) {
                Mail::to($user->email)->bcc(['maria.botella@cmpc.com'])->send(new AppNotificarCotizacionesPorAprobacion($user));
                // Mail::to("gsanchez@invebchile.cl")->send(new AppNotificarCotizacionesPorAprobacion($user));
            }
        }
    }
}
