<?php

namespace App\Mail;

use App\Models\Turno;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class TurnoConfirmadoAdmin extends Mailable
{
    use Queueable;

    public function __construct(public Turno $turno)
    {
    }

    public function build(): self
    {
        return $this->subject('Nuevo turno confirmado')
            ->view('emails.turno-confirmado-admin', [
                'turno' => $this->turno,
            ]);
    }
}
