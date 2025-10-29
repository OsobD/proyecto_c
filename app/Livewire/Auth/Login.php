<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    protected $messages = [
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'Ingresa un correo electrónico válido.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            request()->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        $this->addError('email', 'Las credenciales no coinciden con nuestros registros.');
    }

    #[Layout('components.layouts.auth')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
