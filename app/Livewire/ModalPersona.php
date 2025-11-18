<?php

namespace App\Livewire;

use App\Models\Persona;
use App\Models\TarjetaResponsabilidad;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ModalPersona extends Component
{
    public $showModal = false;
    public $nombres = '';
    public $apellidos = '';
    public $dpi = '';
    public $telefono = '';
    public $correo = '';

    protected $listeners = ['abrirModalPersona' => 'abrir'];

    protected function rules()
    {
        return [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'dpi' => 'required|string|size:13|unique:persona,dpi',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
        ];
    }

    protected $messages = [
        'nombres.required' => 'Los nombres son obligatorios.',
        'apellidos.required' => 'Los apellidos son obligatorios.',
        'dpi.required' => 'El DPI es obligatorio.',
        'dpi.size' => 'El DPI debe tener exactamente 13 dígitos.',
        'dpi.unique' => 'Este DPI ya está registrado.',
        'correo.email' => 'El correo debe ser una dirección válida.',
    ];

    public function abrir()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->showModal = true;
    }

    public function cerrar()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function guardar()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Crear la nueva persona
            $persona = Persona::create([
                'nombres' => $this->nombres,
                'apellidos' => $this->apellidos,
                'dpi' => $this->dpi,
                'telefono' => $this->telefono,
                'correo' => $this->correo,
                'estado' => true,
            ]);

            // Crear tarjeta de responsabilidad
            TarjetaResponsabilidad::create([
                'nombre' => "{$this->nombres} {$this->apellidos}",
                'fecha_creacion' => now(),
                'total' => 0,
                'id_persona' => $persona->id,
                'activo' => true,
            ]);

            DB::commit();

            // Emitir evento para notificar que se creó la persona
            $this->dispatch('personaCreada', [
                'id' => $persona->id,
                'nombre_completo' => "{$persona->nombres} {$persona->apellidos}",
                'dpi' => $persona->dpi,
            ]);

            // Cerrar modal
            $this->showModal = false;
            $this->resetForm();

            session()->flash('message', 'Persona creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al crear la persona: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->nombres = '';
        $this->apellidos = '';
        $this->dpi = '';
        $this->telefono = '';
        $this->correo = '';
    }

    public function render()
    {
        return view('livewire.modal-persona');
    }
}
