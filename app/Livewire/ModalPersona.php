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
        'dpi.unique' => 'Ya existe una persona registrada con este DPI. El DPI debe ser único.',
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
        // Validar los datos
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
            // IMPORTANTE: created_by y updated_by deben ser NULL ya que
            // la foreign key apunta a 'users' pero usamos la tabla 'usuario'
            TarjetaResponsabilidad::create([
                'nombre' => "{$this->nombres} {$this->apellidos}",
                'fecha_creacion' => now(),
                'total' => 0,
                'id_persona' => $persona->id,
                'activo' => true,
                'created_by' => null,
                'updated_by' => null,
            ]);

            DB::commit();

            // Preparar datos para enviar
            $personaData = [
                'id' => $persona->id,
                'nombre_completo' => "{$persona->nombres} {$persona->apellidos}",
                'dpi' => $persona->dpi,
            ];

            // Cerrar modal y resetear formulario
            $this->showModal = false;
            $this->resetForm();

            // Mensaje de éxito
            $mensaje = "Persona '{$persona->nombres} {$persona->apellidos}' creada exitosamente con tarjeta de responsabilidad.";

            // Emitir evento para notificar que se creó la persona (incluyendo el mensaje)
            $this->dispatch('personaCreada', personaData: $personaData, mensaje: $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log del error para debugging
            \Log::error('Error al crear persona: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

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
