<?php

namespace App\Mail;

use App\Models\Turno;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class TurnoConfirmadoCliente extends Mailable
{
    use Queueable;

    public function __construct(public Turno $turno)
    {
    }

    public function build(): self
    {
        return $this->subject('Tu turno fue confirmado')
            ->view('emails.turno-confirmado-cliente', [
                'turno' => $this->turno,
            ]);
    }
}
