<?php

namespace App\Livewire;

use App\Models\Persona;
use App\Models\Bitacora;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class GestionPersonas extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $showAllPersonas = false; // Para mostrar inactivas también

    // Campos del formulario
    public $personaId;
    public $nombres;
    public $apellidos;
    public $telefono;
    public $correo;
    public $fecha_nacimiento;
    public $genero;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:20',
        'correo' => 'nullable|email|max:255',
        'fecha_nacimiento' => 'nullable|date',
        'genero' => 'nullable|in:M,F',
    ];

    protected $messages = [
        'nombres.required' => 'Los nombres son obligatorios.',
        'apellidos.required' => 'Los apellidos son obligatorios.',
        'correo.email' => 'El correo debe ser una dirección válida.',
        'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser válida.',
        'genero.in' => 'El género debe ser Masculino o Femenino.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Persona::query();

        // Filtrar por estado si no queremos ver todas
        if (!$this->showAllPersonas) {
            $query->where('estado', true);
        }

        $personas = $query->where(function($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                  ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                  ->orWhere('correo', 'like', '%' . $this->search . '%')
                  ->orWhere('telefono', 'like', '%' . $this->search . '%');
            })
            ->orderBy('apellidos', 'asc')
            ->orderBy('nombres', 'asc')
            ->paginate(10);

        return view('livewire.gestion-personas', [
            'personas' => $personas
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $persona = Persona::findOrFail($id);

        $this->personaId = $persona->id;
        $this->nombres = $persona->nombres;
        $this->apellidos = $persona->apellidos;
        $this->telefono = $persona->telefono;
        $this->correo = $persona->correo;
        $this->fecha_nacimiento = $persona->fecha_nacimiento;
        $this->genero = $persona->genero;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $persona = Persona::findOrFail($this->personaId);
                $persona->update([
                    'nombres' => $this->nombres,
                    'apellidos' => $this->apellidos,
                    'telefono' => $this->telefono,
                    'correo' => $this->correo,
                    'fecha_nacimiento' => $this->fecha_nacimiento,
                    'genero' => $this->genero,
                ]);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Actualizar',
                    'modelo' => 'Persona',
                    'modelo_id' => $persona->id,
                    'descripcion' => "Persona actualizada: {$persona->nombres} {$persona->apellidos}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                $mensaje = 'Persona actualizada correctamente.';
            } else {
                $persona = Persona::create([
                    'nombres' => $this->nombres,
                    'apellidos' => $this->apellidos,
                    'telefono' => $this->telefono,
                    'correo' => $this->correo,
                    'fecha_nacimiento' => $this->fecha_nacimiento,
                    'genero' => $this->genero,
                    'estado' => true,
                ]);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Crear',
                    'modelo' => 'Persona',
                    'modelo_id' => $persona->id,
                    'descripcion' => "Persona creada: {$persona->nombres} {$persona->apellidos}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                $mensaje = 'Persona creada correctamente.';
            }

            $this->closeModal();
            $this->reset(['search']);
            session()->flash('message', $mensaje);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar la persona: ' . $e->getMessage());
        }
    }

    public function toggleEstado($id)
    {
        try {
            $persona = Persona::with(['tarjetasResponsabilidad', 'usuario'])->findOrFail($id);

            // Si está activa y queremos desactivarla, verificar relaciones
            if ($persona->estado) {
                $tieneTarjetas = $persona->tarjetasResponsabilidad()->where('activo', true)->exists();
                $tieneUsuario = $persona->usuario()->exists();

                if ($tieneTarjetas) {
                    session()->flash('error', 'No se puede desactivar la persona porque tiene tarjetas de responsabilidad activas.');
                    return;
                }

                // Verificar salidas solo si la columna id_persona existe
                try {
                    $tieneSalidas = $persona->salidas()->exists();
                    if ($tieneSalidas) {
                        session()->flash('error', 'No se puede desactivar la persona porque tiene salidas registradas.');
                        return;
                    }
                } catch (\Exception $e) {
                    // Columna no existe aún
                }

                if ($tieneUsuario) {
                    session()->flash('error', 'No se puede desactivar la persona porque tiene un usuario asociado.');
                    return;
                }
            }

            // Cambiar estado
            $nuevoEstado = !$persona->estado;
            $persona->update(['estado' => $nuevoEstado]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => $nuevoEstado ? 'Activar' : 'Desactivar',
                'modelo' => 'Persona',
                'modelo_id' => $persona->id,
                'descripcion' => ($nuevoEstado ? 'Persona activada: ' : 'Persona desactivada: ') . "{$persona->nombres} {$persona->apellidos}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', $nuevoEstado ? 'Persona activada correctamente.' : 'Persona desactivada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->personaId = null;
        $this->nombres = '';
        $this->apellidos = '';
        $this->telefono = '';
        $this->correo = '';
        $this->fecha_nacimiento = '';
        $this->genero = '';
        $this->resetErrorBag();
    }
}
