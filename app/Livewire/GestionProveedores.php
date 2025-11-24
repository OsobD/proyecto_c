<?php

namespace App\Livewire;

use App\Models\Bitacora;
use App\Models\Proveedor;
use App\Models\RegimenTributario;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Componente GestionProveedores
 *
 * Gestiona el CRUD completo de proveedores del sistema de inventario.
 * Permite crear, editar, buscar y activar/desactivar proveedores.
 *
 * **Funcionalidades principales:**
 * - Listado de proveedores con búsqueda en tiempo real
 * - Creación y edición de proveedores mediante modal
 * - Asociación de proveedores con régimen tributario
 * - Activación/desactivación de proveedores (soft delete)
 *
 * @package App\Livewire
 * @version 2.0
 * @see resources/views/livewire/gestion-proveedores.blade.php Vista asociada
 */
class GestionProveedores extends Component
{
    use WithPagination;

    // Propiedades de búsqueda y filtrado
    /** @var string Término de búsqueda para filtrar proveedores */
    public $searchProveedor = '';

    // Modal de filtros
    public $showFilterModal = false;
    public $showInactive = false;

    // Ordenamiento
    public $sortField = 'nombre';
    public $sortDirection = 'asc';

    // Propiedades de control de UI
    /** @var bool Controla visibilidad del modal de proveedor */
    public $showModal = false;

    /** @var bool Controla visibilidad del dropdown de régimen */
    public $showRegimenDropdown = false;

    /** @var string|null ID del proveedor en edición (null = modo creación) */
    public $editingId = null;

    // Campos del formulario de proveedor
    /** @var string NIT del proveedor */
    public $nit = '';

    /** @var string|int ID del régimen tributario seleccionado */
    public $regimenTributarioId = '';

    /** @var string|null Nombre del régimen seleccionado para mostrar en el dropdown */
    public $selectedRegimen = null;

    /** @var string Nombre del proveedor */
    public $nombre = '';

    /**
     * Resetea la paginación cuando cambia la búsqueda
     *
     * @return void
     */
    public function updatingSearchProveedor()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField !== $field) {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        } else {
            if ($this->sortDirection === 'asc') {
                $this->sortDirection = 'desc';
            } elseif ($this->sortDirection === 'desc') {
                $this->sortField = null;
                $this->sortDirection = null;
            }
        }
        $this->resetPage();
    }

    public function openFilterModal()
    {
        $this->showFilterModal = true;
    }

    public function closeFilterModal()
    {
        $this->showFilterModal = false;
    }

    public function clearFilters()
    {
        $this->showInactive = false;
        $this->sortField = 'nombre';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    /**
     * Renderiza la vista del componente con datos desde BD
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Cargar proveedores con sus relaciones
        $query = Proveedor::with('regimenTributario');

        // Filtrar por estado
        if (!$this->showInactive) {
            $query->where('activo', true);
        }

        // Aplicar búsqueda
        if ($this->searchProveedor) {
            $search = strtolower(trim($this->searchProveedor));
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw('LOWER(nombre)'), 'like', "%{$search}%")
                    ->orWhere(DB::raw('LOWER(nit)'), 'like', "%{$search}%")
                    ->orWhereHas('regimenTributario', function ($subQ) use ($search) {
                        $subQ->where(DB::raw('LOWER(nombre)'), 'like', "%{$search}%");
                    });
            });
        }

        // Aplicar ordenamiento
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('nombre');
        }

        $proveedores = $query->paginate(10);

        $regimenesTributarios = RegimenTributario::orderBy('nombre')
            ->get();

        return view('livewire.gestion-proveedores', [
            'proveedores' => $proveedores,
            'regimenesTributarios' => $regimenesTributarios,
        ]);
    }

    /**
     * Abre el modal de proveedor en modo creación
     *
     * @return void
     */
    public function abrirModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Abre el modal de proveedor en modo edición
     *
     * Carga los datos del proveedor seleccionado en el formulario.
     *
     * @param int $id ID del proveedor a editar
     * @return void
     */
    public function editarProveedor($id)
    {
        $proveedor = Proveedor::find($id);

        if ($proveedor) {
            $this->editingId = $id;
            $this->nit = $proveedor->nit;
            $this->regimenTributarioId = $proveedor->id_regimen_tributario;
            $this->selectedRegimen = $proveedor->regimenTributario->nombre ?? null;
            $this->nombre = $proveedor->nombre;
            $this->showModal = true;
        }
    }

    /**
     * Guarda un proveedor (crear o actualizar según editingId)
     *
     * Valida los campos del formulario y persiste los cambios.
     * Muestra mensaje de éxito mediante flash session.
     *
     * @return void
     */
    public function guardarProveedor()
    {
        $rules = [
            'nit' => 'required|numeric|digits_between:5,20',
            'regimenTributarioId' => 'required|exists:regimen_tributario,id',
            'nombre' => 'required|min:3|max:255',
        ];

        // Validar que el NIT sea único, excluyendo el ID actual si estamos editando
        $rules['nit'] .= '|unique:proveedor,nit' . ($this->editingId ? ',' . $this->editingId : '');

        $this->validate($rules, [
            'nit.required' => 'El NIT es obligatorio.',
            'nit.numeric' => 'El NIT debe contener solo números.',
            'nit.digits_between' => 'El NIT debe tener entre 5 y 20 dígitos.',
            'nit.unique' => 'Este NIT ya está registrado.',
            'regimenTributarioId.required' => 'Debe seleccionar un régimen tributario.',
            'regimenTributarioId.exists' => 'El régimen tributario seleccionado no existe.',
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
        ]);

        if ($this->editingId) {
            // Actualizar proveedor existente
            $proveedor = Proveedor::find($this->editingId);
            if ($proveedor) {
                $proveedor->nit = $this->nit;
                $proveedor->id_regimen_tributario = $this->regimenTributarioId;
                $proveedor->nombre = $this->nombre;
                $proveedor->save();

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Actualizar',
                    'modelo' => 'Proveedor',
                    'modelo_id' => $proveedor->id,
                    'descripcion' => "Proveedor actualizado: {$proveedor->nombre}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                session()->flash('message', 'Proveedor actualizado exitosamente.');
            }
        } else {
            // Crear nuevo proveedor
            $proveedor = Proveedor::create([
                'nit' => $this->nit,
                'id_regimen_tributario' => $this->regimenTributarioId,
                'nombre' => $this->nombre,
                'activo' => true,
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Crear',
                'modelo' => 'Proveedor',
                'modelo_id' => $proveedor->id,
                'descripcion' => "Proveedor creado: {$proveedor->nombre}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Proveedor creado exitosamente.');
        }

        $this->closeModal();
    }

    /**
     * Cambia el estado activo/inactivo de un proveedor (soft delete)
     *
     * @param int $id ID del proveedor a activar/desactivar
     * @return void
     */
    public function toggleEstado($id)
    {
        $proveedor = Proveedor::find($id);
        if ($proveedor) {
            $proveedor->activo = !$proveedor->activo;
            $proveedor->save();

            // Registrar en bitácora
            Bitacora::create([
                'accion' => $proveedor->activo ? 'Activar' : 'Desactivar',
                'modelo' => 'Proveedor',
                'modelo_id' => $proveedor->id,
                'descripcion' => "Proveedor " . ($proveedor->activo ? 'activado' : 'desactivado') . ": {$proveedor->nombre}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Estado del proveedor actualizado.');
        }
    }

    /**
     * Selecciona un régimen tributario del dropdown
     *
     * @param int $regimenId ID del régimen tributario
     * @param string $regimenNombre Nombre del régimen tributario
     * @return void
     */
    public function selectRegimen($regimenId, $regimenNombre)
    {
        $this->regimenTributarioId = $regimenId;
        $this->selectedRegimen = $regimenNombre;
        $this->showRegimenDropdown = false;
    }

    /**
     * Limpia la selección de régimen tributario
     *
     * @return void
     */
    public function clearRegimen()
    {
        $this->regimenTributarioId = '';
        $this->selectedRegimen = null;
    }

    /**
     * Cierra el modal principal de proveedor
     *
     * @return void
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Limpia los campos del formulario y errores de validación
     *
     * @return void
     */
    private function resetForm()
    {
        $this->editingId = null;
        $this->nit = '';
        $this->regimenTributarioId = '';
        $this->selectedRegimen = null;
        $this->nombre = '';
        $this->showRegimenDropdown = false;
        $this->resetErrorBag();
    }
}
