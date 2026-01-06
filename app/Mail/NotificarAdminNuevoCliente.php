<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificarAdminNuevoCliente extends Mailable
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
        $address = 'no-reply@invebchile.cl';
        $subject = 'Nuevo Cliente Registrado';
        $name = 'CMPC';

        return $this->view('email.notificarAdminNuevoCliente')
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject);
    }
}
