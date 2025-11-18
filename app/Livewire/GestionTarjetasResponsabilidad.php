<?php

namespace App\Livewire;

use App\Models\Persona;
use App\Models\TarjetaResponsabilidad;
use App\Models\Bitacora;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class GestionTarjetasResponsabilidad extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;

    // Campos del formulario
    public $tarjetaId;
    public $id_persona;
    public $fecha_creacion;
    public $total = 0;

    // Para crear persona nueva con la tarjeta
    public $nombres;
    public $apellidos;
    public $dpi;

    // Para mostrar la persona seleccionada en el modal
    public $personaSeleccionada = null;

    // Para el modal de productos
    public $showProductosModal = false;
    public $tarjetaProductos = [];
    public $tarjetaNombre = '';

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'dpi' => 'required|string|size:13',
        'fecha_creacion' => 'required|date',
    ];

    protected $messages = [
        'nombres.required' => 'Los nombres son obligatorios.',
        'apellidos.required' => 'Los apellidos son obligatorios.',
        'dpi.required' => 'El DPI es obligatorio.',
        'dpi.size' => 'El DPI debe tener exactamente 13 dígitos.',
        'fecha_creacion.required' => 'La fecha de creación es obligatoria.',
        'fecha_creacion.date' => 'La fecha de creación debe ser válida.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function render()
    {
        $tarjetas = TarjetaResponsabilidad::with('persona')
            ->where('activo', true)
            ->whereHas('persona', function($query) {
                $query->where('estado', true)
                      ->where(function($q) {
                          $q->where('nombres', 'like', '%' . $this->search . '%')
                            ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('fecha_creacion', 'desc')
            ->paginate(10);

        return view('livewire.gestion-tarjetas-responsabilidad', [
            'tarjetas' => $tarjetas
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->fecha_creacion = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function edit($id)
    {
        $tarjeta = TarjetaResponsabilidad::with('persona')->findOrFail($id);

        $this->tarjetaId = $tarjeta->id;
        $this->id_persona = $tarjeta->id_persona;
        $this->fecha_creacion = $tarjeta->fecha_creacion->format('Y-m-d');
        $this->nombres = $tarjeta->persona->nombres;
        $this->apellidos = $tarjeta->persona->apellidos;
        $this->dpi = $tarjeta->persona->dpi;

        // Establecer la persona seleccionada para mostrar en el modal
        $this->personaSeleccionada = $tarjeta->persona;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        // Validar DPI único
        $rules = $this->rules;
        $rules['dpi'] = 'required|string|size:13|unique:persona,dpi';

        $this->validate($rules);

        try {
            if ($this->editMode) {
                $tarjeta = TarjetaResponsabilidad::findOrFail($this->tarjetaId);

                $tarjeta->update([
                    'fecha_creacion' => $this->fecha_creacion,
                    'updated_by' => Auth::id(),
                ]);

                $persona = Persona::find($tarjeta->id_persona);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Actualizar',
                    'modelo' => 'TarjetaResponsabilidad',
                    'modelo_id' => $tarjeta->id,
                    'descripcion' => "Tarjeta de responsabilidad actualizada para: {$persona->nombres} {$persona->apellidos}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                $mensaje = 'Tarjeta de responsabilidad actualizada correctamente.';
            } else {
                // Primero crear la persona
                $persona = Persona::create([
                    'nombres' => $this->nombres,
                    'apellidos' => $this->apellidos,
                    'dpi' => $this->dpi,
                    'estado' => true,
                ]);

                // Luego crear la tarjeta para esa persona
                $tarjeta = TarjetaResponsabilidad::create([
                    'nombre' => "{$this->nombres} {$this->apellidos}",
                    'id_persona' => $persona->id,
                    'fecha_creacion' => $this->fecha_creacion,
                    'total' => 0,
                    'activo' => true,
                    'created_by' => Auth::id(),
                ]);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Crear',
                    'modelo' => 'TarjetaResponsabilidad',
                    'modelo_id' => $tarjeta->id,
                    'descripcion' => "Tarjeta y persona creadas: {$persona->nombres} {$persona->apellidos}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                $mensaje = 'Persona y tarjeta de responsabilidad creadas correctamente.';
            }

            $this->closeModal();
            $this->dispatch('tarjeta-saved');
            session()->flash('message', $mensaje);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar la tarjeta: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $tarjeta = TarjetaResponsabilidad::with([
            'tarjetasProducto',
            'entradas',
            'salidas',
            'traslados',
            'devoluciones'
        ])->findOrFail($id);

        // Verificar si tiene relaciones activas
        $tieneProductos = $tarjeta->tarjetasProducto()->exists();
        $tieneEntradas = $tarjeta->entradas()->exists();
        $tieneSalidas = $tarjeta->salidas()->exists();
        $tieneTraslados = $tarjeta->traslados()->exists();
        $tieneDevoluciones = $tarjeta->devoluciones()->exists();

        if ($tieneProductos) {
            session()->flash('error', 'No se puede desactivar la tarjeta porque tiene productos asignados.');
            return;
        }

        if ($tieneEntradas) {
            session()->flash('error', 'No se puede desactivar la tarjeta porque tiene entradas registradas.');
            return;
        }

        if ($tieneSalidas) {
            session()->flash('error', 'No se puede desactivar la tarjeta porque tiene salidas registradas.');
            return;
        }

        if ($tieneTraslados) {
            session()->flash('error', 'No se puede desactivar la tarjeta porque tiene traslados registrados.');
            return;
        }

        if ($tieneDevoluciones) {
            session()->flash('error', 'No se puede desactivar la tarjeta porque tiene devoluciones registradas.');
            return;
        }

        $this->tarjetaId = $id;
        $this->dispatch('confirm-delete');
    }

    public function delete()
    {
        try {
            $tarjeta = TarjetaResponsabilidad::with('persona')->findOrFail($this->tarjetaId);

            $tarjeta->update([
                'activo' => false,
                'updated_by' => Auth::id(),
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Desactivar',
                'descripcion' => "Tarjeta de responsabilidad desactivada para: {$tarjeta->persona->nombres} {$tarjeta->persona->apellidos}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Tarjeta de responsabilidad desactivada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al desactivar la tarjeta: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->tarjetaId = null;
        $this->id_persona = null;
        $this->nombres = '';
        $this->apellidos = '';
        $this->dpi = '';
        $this->fecha_creacion = '';
        $this->total = 0;
        $this->personaSeleccionada = null;
        $this->resetErrorBag();
    }

    /**
     * Muestra el modal con los productos asignados a una tarjeta
     */
    public function verProductos($tarjetaId)
    {
        $tarjeta = TarjetaResponsabilidad::with(['persona', 'tarjetasProducto.producto'])
            ->findOrFail($tarjetaId);

        $this->tarjetaNombre = "{$tarjeta->persona->nombres} {$tarjeta->persona->apellidos}";
        $this->tarjetaProductos = $tarjeta->tarjetasProducto->map(function($tp) {
            return [
                'id' => $tp->id,
                'producto' => $tp->producto->nombre,
                'cantidad' => $tp->cantidad,
                'fecha_asignacion' => $tp->fecha_asignacion ? \Carbon\Carbon::parse($tp->fecha_asignacion)->format('d/m/Y') : 'N/A',
                'estado' => $tp->estado,
            ];
        })->toArray();

        $this->showProductosModal = true;
    }

    /**
     * Cierra el modal de productos
     */
    public function cerrarProductosModal()
    {
        $this->showProductosModal = false;
        $this->tarjetaProductos = [];
        $this->tarjetaNombre = '';
    }
}
