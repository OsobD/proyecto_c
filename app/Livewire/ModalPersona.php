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
    public $errorMessage = ''; // Para mostrar errores sin cerrar el modal

    protected $listeners = ['abrirModalPersona' => 'abrir'];

    protected function rules()
    {
        return [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'dpi' => 'required|digits:13|unique:persona,dpi',
            'telefono' => 'nullable|digits:8|unique:persona,telefono',
            'correo' => 'nullable|email:rfc,dns|max:255|unique:persona,correo',
        ];
    }

    protected $messages = [
        'nombres.required' => 'Los nombres son obligatorios.',
        'apellidos.required' => 'Los apellidos son obligatorios.',
        'dpi.required' => 'El DPI es obligatorio.',
        'dpi.digits' => 'El DPI debe tener exactamente 13 dígitos numéricos.',
        'dpi.unique' => 'Ya existe una persona registrada con este DPI.',
        'telefono.digits' => 'El teléfono debe tener exactamente 8 dígitos numéricos.',
        'telefono.unique' => 'Ya existe una persona registrada con este teléfono.',
        'correo.email' => 'El correo debe ser una dirección de email válida (ej: usuario@dominio.com).',
        'correo.unique' => 'Ya existe una persona registrada con este correo.',
    ];

    public function abrir()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->errorMessage = ''; // Limpiar mensajes de error
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
        // Limpiar mensaje de error previo
        $this->errorMessage = '';

        // Preparar reglas de validación dinámicas
        $validationRules = $this->rules();

        // Si teléfono está vacío, quitar validación unique y digits
        if (empty($this->telefono)) {
            $validationRules['telefono'] = 'nullable';
        }

        // Si correo está vacío, quitar validación unique y email
        if (empty($this->correo)) {
            $validationRules['correo'] = 'nullable';
        }

        // Validar los datos con las reglas ajustadas
        $this->validate($validationRules);

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

            // Mostrar error en el modal sin cerrarlo
            $this->errorMessage = 'Error al crear la persona: ' . $e->getMessage();
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
