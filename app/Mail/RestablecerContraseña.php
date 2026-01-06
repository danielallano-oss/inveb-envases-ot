<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use App\User;

class RestablecerContraseña extends Mailable
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
        $subject = 'Recuperar Contraseña';
        $name = 'CMPC';
        $rut=$this->data;
        $token = Str::random(64);       
        $token_expire=Carbon::now()->addMinutes(5);
        
        $user_update=User::where('rut', $rut)->update([
                            'token_reset_password' => $token,
                            'token_reset_password_expire' => $token_expire,
                            ]);
        
        return $this->view('email.restablecerContraseña', compact('token'))
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject);
    }
}
