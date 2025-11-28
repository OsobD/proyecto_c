<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Bitacora;

class CambiarContrasena extends Component
{
    public $contrasena_actual = '';
    public $contrasena_nueva = '';
    public $contrasena_confirmacion = '';

    protected function rules()
    {
        return [
            'contrasena_actual' => 'required',
            'contrasena_nueva' => [
                'required',
                'min:8',
                'regex:/[a-z]/',      // Al menos una minúscula
                'regex:/[A-Z]/',      // Al menos una mayúscula
                'regex:/[0-9]/',      // Al menos un número
                'regex:/[@$!%*#?&]/', // Al menos un carácter especial
                'different:contrasena_actual',
            ],
            'contrasena_confirmacion' => 'required|same:contrasena_nueva',
        ];
    }

    protected $messages = [
        'contrasena_actual.required' => 'La contraseña actual es obligatoria.',
        'contrasena_nueva.required' => 'La nueva contraseña es obligatoria.',
        'contrasena_nueva.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'contrasena_nueva.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%*#?&).',
        'contrasena_nueva.different' => 'La nueva contraseña debe ser diferente a la actual.',
        'contrasena_confirmacion.required' => 'Debe confirmar la nueva contraseña.',
        'contrasena_confirmacion.same' => 'Las contraseñas no coinciden.',
    ];

    public function cambiarContrasena()
    {
        $this->validate();

        $usuario = Auth::user();

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($this->contrasena_actual, $usuario->contrasena)) {
            $this->addError('contrasena_actual', 'La contraseña actual es incorrecta.');
            return;
        }

        try {
            DB::beginTransaction();

            // Actualizar contraseña y marcar que ya no necesita cambiarla
            $usuario->update([
                'contrasena' => Hash::make($this->contrasena_nueva),
                'debe_cambiar_contrasena' => false,
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Cambiar Contraseña',
                'modelo' => 'Usuario',
                'modelo_id' => $usuario->id,
                'descripcion' => "Usuario {$usuario->nombre_usuario} cambió su contraseña (primer login)",
                'id_usuario' => $usuario->id,
                'created_at' => now(),
            ]);

            DB::commit();

            session()->flash('message', 'Contraseña actualizada exitosamente.');

            // Redirigir a inicio
            return redirect()->to('/inicio');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al cambiar la contraseña: ' . $e->getMessage());
        }
    }

    #[Layout('components.layouts.auth')]
    public function render()
    {
        return view('livewire.auth.cambiar-contrasena');
    }
}
