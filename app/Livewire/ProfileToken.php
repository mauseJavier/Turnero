<?php

namespace App\Livewire;

use Livewire\Component;


use Illuminate\Support\Facades\Auth;

class ProfileToken extends Component
{
    public $token = null;

    public function generateToken()
    {
        $user = Auth::user();
        // Genera un token usando Sanctum
        $this->token = $user->createToken('profile-token')->plainTextToken;
    }

    public function render()
    {
        return view('livewire.profile-token');
    }
}
