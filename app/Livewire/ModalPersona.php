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

    public function testComponente()
    {
        // Método de prueba para verificar que el componente funciona
        $this->js("alert('El componente ModalPersona está funcionando correctamente');");
    }

    public function guardar()
    {
        // Log inicial para debugging
        \Log::info('ModalPersona::guardar() - Inicio', [
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'dpi' => $this->dpi,
        ]);

        // Validar los datos
        try {
            $validatedData = $this->validate();
            \Log::info('ModalPersona::guardar() - Validación exitosa');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('ModalPersona::guardar() - Error de validación', [
                'errors' => $e->errors()
            ]);
            // Re-lanzar la excepción para que Livewire la maneje
            throw $e;
        }

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

            \Log::info('ModalPersona::guardar() - Persona creada', ['id' => $persona->id]);

            // Crear tarjeta de responsabilidad
            // IMPORTANTE: created_by y updated_by deben ser NULL ya que
            // la foreign key apunta a 'users' pero usamos la tabla 'usuario'
            $tarjetaData = [
                'nombre' => "{$this->nombres} {$this->apellidos}",
                'fecha_creacion' => now(),
                'total' => 0,
                'id_persona' => $persona->id,
                'activo' => true,
                'created_by' => null,
                'updated_by' => null,
            ];

            TarjetaResponsabilidad::create($tarjetaData);

            \Log::info('ModalPersona::guardar() - Tarjeta de responsabilidad creada');

            DB::commit();

            // Preparar datos para enviar
            $personaData = [
                'id' => $persona->id,
                'nombre_completo' => "{$persona->nombres} {$persona->apellidos}",
                'dpi' => $persona->dpi,
            ];

            // Cerrar modal ANTES de disparar eventos
            $this->showModal = false;
            $this->resetForm();

            // Emitir evento para notificar que se creó la persona
            $this->dispatch('personaCreada', personaData: $personaData);

            \Log::info('ModalPersona::guardar() - Evento personaCreada disparado', $personaData);

            // JavaScript toast notification
            $this->js("
                console.log('Persona creada:', " . json_encode($personaData) . ");
                alert('Persona creada exitosamente: {$persona->nombres} {$persona->apellidos}');
            ");

        } catch (\Exception $e) {
            DB::rollBack();

            // Log del error para debugging
            \Log::error('ModalPersona::guardar() - Error al crear persona', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->js("alert('Error al crear la persona: " . addslashes($e->getMessage()) . "');");
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
