<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificarActualizacionMatriz extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        //obtener fecha con formato dd/mm/YYYY
        $date_viernes = Carbon::now()->format('d/m/Y');

        $address = 'no-reply@invebchile.cl';
        $subject = 'Recordatorio: Actualizar la base datos matrices al '.$date_viernes.'';
        $name = 'CMPC';

        return $this->view('email.notificarActualizacionMatriz')
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject);
    }
}
