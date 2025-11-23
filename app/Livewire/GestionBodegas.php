<?php

namespace App\Livewire;

use App\Models\Bitacora;
use App\Models\Bodega;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Categoria;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Componente GestionBodegas
 *
 * Gestiona el CRUD de bodegas físicas del sistema de inventario
 * y permite visualizar y gestionar los productos (lotes) de cada bodega.
 *
 * @package App\Livewire
 * @see resources/views/livewire/gestion-bodegas.blade.php
 */
class GestionBodegas extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;

    // Modal de filtros
    public $showFilterModal = false;
    public $showInactive = false;

    // Ordenamiento
    public $sortField = 'nombre';
    public $sortDirection = 'asc';

    // Campos del formulario de bodega
    public $bodegaId;
    public $nombre;

    // Control de expansión de productos por bodega
    public $bodegaIdProductosExpandido = null;

    // Control del modal de producto
    public $showModalProducto = false;
    public $editingProductoId = null;

    // Campos del formulario de producto
    public $codigo = '';
    public $descripcion = '';
    public $categoriaId = '';
    public $esConsumible = false;

    // Control del sub-modal de categoría
    public $showSubModalCategoria = false;
    public $nuevaCategoriaNombre = '';
    public $searchCategoria = '';
    public $showCategoriaDropdown = false;
    public $selectedCategoria = null;

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'nombre' => 'required|string|max:255',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre de la bodega es obligatorio.',
        'nombre.max' => 'El nombre no puede exceder los 255 caracteres.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSearchCategoria()
    {
        $this->showCategoriaDropdown = !empty($this->searchCategoria);
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

    public function render()
    {
        $query = Bodega::query();

        // Filtrar por estado
        if (!$this->showInactive) {
            $query->where('activo', true);
        }

        // Aplicar búsqueda
        if (!empty($this->search)) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        // Aplicar ordenamiento
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('nombre', 'asc');
        }

        $bodegas = $query->paginate(10);

        return view('livewire.gestion-bodegas', [
            'bodegas' => $bodegas
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
        $bodega = Bodega::findOrFail($id);

        $this->bodegaId = $bodega->id;
        $this->nombre = $bodega->nombre;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $bodega = Bodega::findOrFail($this->bodegaId);
                $bodega->update([
                    'nombre' => $this->nombre,
                    'updated_by' => Auth::id(),
                ]);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Actualizar',
                    'descripcion' => "Bodega actualizada: {$bodega->nombre}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                session()->flash('message', 'Bodega actualizada correctamente.');
            } else {
                $bodega = Bodega::create([
                    'nombre' => $this->nombre,
                    'activo' => true,
                    'created_by' => Auth::id(),
                ]);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Crear',
                    'descripcion' => "Bodega creada: {$bodega->nombre}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                session()->flash('message', 'Bodega creada correctamente.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar la bodega: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $bodega = Bodega::with([
            'lotes',
            'compras',
            'entradas',
            'devoluciones',
            'traslados',
            'salidas'
        ])->findOrFail($id);

        // Verificar si tiene relaciones activas
        $tieneLotes = $bodega->lotes()->exists();
        $tieneCompras = $bodega->compras()->exists();
        $tieneEntradas = $bodega->entradas()->exists();
        $tieneDevoluciones = $bodega->devoluciones()->exists();
        $tieneTraslados = $bodega->traslados()->exists();
        $tieneSalidas = $bodega->salidas()->exists();

        if ($tieneLotes || $tieneCompras || $tieneEntradas || $tieneDevoluciones || $tieneTraslados || $tieneSalidas) {
            session()->flash('error', 'No se puede desactivar la bodega porque tiene movimientos asociados (lotes, compras, entradas, salidas, etc.).');
            return;
        }

        $this->bodegaId = $id;
        $this->dispatch('confirm-delete');
    }

    public function delete()
    {
        try {
            $bodega = Bodega::findOrFail($this->bodegaId);

            $bodega->update([
                'activo' => false,
                'updated_by' => Auth::id(),
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Desactivar',
                'descripcion' => "Bodega desactivada: {$bodega->nombre}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Bodega desactivada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al desactivar la bodega: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->bodegaId = null;
        $this->nombre = '';
        $this->resetErrorBag();
    }

    // ==================== MÉTODOS PARA GESTIÓN DE PRODUCTOS EN BODEGA ====================

    /**
     * Expande/colapsa los productos de una bodega
     */
    public function toggleProductos($id)
    {
        $this->bodegaIdProductosExpandido = $this->bodegaIdProductosExpandido === $id ? null : $id;
    }

    /**
     * Abre el modal para crear un nuevo producto
     */
    public function abrirModalProducto()
    {
        $this->resetFormProducto();
        $this->showModalProducto = true;
    }

    /**
     * Obtiene las categorías activas filtradas para el dropdown
     */
    public function getCategoriaResultsProperty()
    {
        if (empty($this->searchCategoria)) {
            return [];
        }

        $search = strtolower(trim($this->searchCategoria));

        return Categoria::where('activo', true)
            ->where(DB::raw('LOWER(nombre)'), 'like', "%{$search}%")
            ->limit(6)
            ->get()
            ->map(function($categoria) {
                return [
                    'id' => $categoria->id,
                    'nombre' => $categoria->nombre,
                ];
            })
            ->toArray();
    }

    /**
     * Selecciona una categoría del dropdown
     */
    public function selectCategoria($categoriaId)
    {
        $categoria = Categoria::find($categoriaId);

        if ($categoria) {
            $this->selectedCategoria = [
                'id' => $categoria->id,
                'nombre' => $categoria->nombre,
            ];

            $this->categoriaId = $categoria->id;
            $this->searchCategoria = '';
            $this->showCategoriaDropdown = false;
        }
    }

    /**
     * Limpia la selección de categoría
     */
    public function clearCategoria()
    {
        $this->selectedCategoria = null;
        $this->categoriaId = '';
        $this->searchCategoria = '';
    }

    /**
     * Abre el sub-modal para crear nueva categoría
     */
    public function abrirSubModalCategoria()
    {
        $this->nuevaCategoriaNombre = '';
        $this->showSubModalCategoria = true;
    }

    /**
     * Guarda una nueva categoría desde el sub-modal
     */
    public function guardarNuevaCategoria()
    {
        $this->validate([
            'nuevaCategoriaNombre' => 'required|min:3|max:100',
        ], [
            'nuevaCategoriaNombre.required' => 'El nombre de la categoría es obligatorio.',
            'nuevaCategoriaNombre.min' => 'El nombre debe tener al menos 3 caracteres.',
        ]);

        $categoria = Categoria::create([
            'nombre' => $this->nuevaCategoriaNombre,
            'activo' => true,
        ]);

        $this->selectedCategoria = [
            'id' => $categoria->id,
            'nombre' => $categoria->nombre,
        ];
        $this->categoriaId = $categoria->id;
        $this->showSubModalCategoria = false;
        $this->nuevaCategoriaNombre = '';
    }

    /**
     * Cierra el sub-modal de categoría
     */
    public function closeSubModalCategoria()
    {
        $this->showSubModalCategoria = false;
        $this->nuevaCategoriaNombre = '';
    }

    /**
     * Guarda un producto nuevo
     */
    public function guardarProducto()
    {
        $rules = [
            'codigo' => 'required|min:1|max:50|unique:producto,id',
            'descripcion' => 'required|min:3|max:255',
            'categoriaId' => 'required|exists:categoria,id',
        ];

        $this->validate($rules, [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.min' => 'El código debe tener al menos 1 carácter.',
            'codigo.unique' => 'Este código de producto ya existe.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 3 caracteres.',
            'categoriaId.required' => 'Debe seleccionar una categoría.',
            'categoriaId.exists' => 'La categoría seleccionada no existe.',
        ]);

        try {
            Producto::create([
                'id' => $this->codigo,
                'descripcion' => $this->descripcion,
                'id_categoria' => $this->categoriaId,
                'es_consumible' => $this->esConsumible,
                'activo' => true,
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Crear',
                'descripcion' => "Producto creado: {$this->codigo} - {$this->descripcion}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Producto creado correctamente.');
            $this->closeModalProducto();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Cierra el modal de producto
     */
    public function closeModalProducto()
    {
        $this->showModalProducto = false;
        $this->resetFormProducto();
    }

    /**
     * Limpia los campos del formulario de producto
     */
    private function resetFormProducto()
    {
        $this->editingProductoId = null;
        $this->codigo = '';
        $this->descripcion = '';
        $this->categoriaId = '';
        $this->esConsumible = false;
        $this->selectedCategoria = null;
        $this->searchCategoria = '';
        $this->resetErrorBag();
    }
}
