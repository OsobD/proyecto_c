<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $nombre_usuario = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'nombre_usuario' => 'required|string',
        'password' => 'required|min:6',
    ];

    protected $messages = [
        'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
        'nombre_usuario.string' => 'Ingresa un nombre de usuario válido.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['nombre_usuario' => $this->nombre_usuario, 'password' => $this->password], $this->remember)) {
            request()->session()->regenerate();

            return redirect()->intended('/inicio');
        }

        $this->addError('nombre_usuario', 'Las credenciales no coinciden con nuestros registros.');
    }

    #[Layout('components.layouts.auth')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
