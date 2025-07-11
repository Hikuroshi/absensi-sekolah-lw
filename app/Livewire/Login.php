<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $error = '';

    public function login()
    {
        $this->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $loginField = filter_var($this->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'id_number';
        if (Auth::attempt([$loginField => $this->email, 'password' => $this->password])) {
            session()->regenerate();
            return redirect()->intended(route('dashboard'));
        } else {
            $this->error = 'Email/Nomor atau password salah.';
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}
