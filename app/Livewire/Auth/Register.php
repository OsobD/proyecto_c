<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Register extends Component
{
    public $nombre_usuario = '';
    public $correo = '';
    public $password = '';
    public $password_confirmation = '';
    public $nombres = '';
    public $apellidos = '';
    public $dpi = '';
    public $telefono = '';

    protected $rules = [
        'nombre_usuario' => 'required|string|max:255|unique:usuario,nombre_usuario',
        'correo' => 'required|email|max:255|unique:persona,correo',
        'password' => 'required|min:6|confirmed',
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'dpi' => 'required|digits:13|unique:persona,dpi',
        'telefono' => 'nullable|digits:8|unique:persona,telefono',
    ];

    protected $messages = [
        'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
        'nombre_usuario.unique' => 'Este nombre de usuario ya está registrado.',
        'correo.required' => 'El correo electrónico es obligatorio.',
        'correo.email' => 'Ingresa un correo electrónico válido.',
        'correo.unique' => 'Ya existe una persona registrada con este correo.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'nombres.required' => 'Los nombres son obligatorios.',
        'apellidos.required' => 'Los apellidos son obligatorios.',
        'dpi.required' => 'El DPI es obligatorio.',
        'dpi.digits' => 'El DPI debe tener exactamente 13 dígitos numéricos.',
        'dpi.unique' => 'Ya existe una persona registrada con este DPI.',
        'telefono.digits' => 'El teléfono debe tener exactamente 8 dígitos numéricos.',
        'telefono.unique' => 'Ya existe una persona registrada con este teléfono.',
    ];

    public function register()
    {
        $this->validate();

        // Crear persona
        $persona = Persona::create([
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'dpi' => $this->dpi,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'estado' => true,
        ]);

        // Obtener rol de Operador por defecto
        $rolOperador = Rol::where('nombre', 'Operador')->first();

        // Crear usuario
        $usuario = Usuario::create([
            'nombre_usuario' => $this->nombre_usuario,
            'contrasena' => Hash::make($this->password),
            'id_persona' => $persona->id,
            'id_rol' => $rolOperador?->id,
            'estado' => true,
        ]);

        // Autenticar automáticamente
        Auth::login($usuario);

        return redirect('/dashboard');
    }

    #[Layout('components.layouts.auth')]
    public function render()
    {
        return view('livewire.auth.register');
    }
}
