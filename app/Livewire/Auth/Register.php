<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Persona;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $nombres = '';
    public $apellidos = '';
    public $telefono = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:20',
    ];

    protected $messages = [
        'name.required' => 'El nombre de usuario es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'Ingresa un correo electrónico válido.',
        'email.unique' => 'Este correo ya está registrado.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'nombres.required' => 'Los nombres son obligatorios.',
        'apellidos.required' => 'Los apellidos son obligatorios.',
    ];

    public function register()
    {
        $this->validate();

        // Crear persona
        $persona = Persona::create([
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'telefono' => $this->telefono,
            'correo' => $this->email,
            'estado' => true,
        ]);

        // Obtener rol de Operador por defecto
        $rolOperador = Rol::where('nombre', 'Operador')->first();

        // Crear usuario
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'id_persona' => $persona->id,
            'id_rol' => $rolOperador?->id,
            'estado' => true,
        ]);

        // Autenticar automáticamente
        Auth::login($user);

        return redirect('/dashboard');
    }

    #[Layout('components.layouts.auth')]
    public function render()
    {
        return view('livewire.auth.register');
    }
}
