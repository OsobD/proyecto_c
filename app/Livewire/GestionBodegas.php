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

    // Campos del formulario de bodega
    public $bodegaId;
    public $nombre;

    // Control de expansión de productos por bodega
    public $bodegaIdProductosExpandido = null;
    public $searchProducto = '';

    // Control del modal de lotes
    public $showModalLote = false;
    public $editingLoteId = null;

    // Campos del formulario de lote
    public $loteProductoId = '';
    public $loteCantidad = '';
    public $lotePrecioIngreso = '';
    public $loteFechaIngreso = '';
    public $loteObservaciones = '';

    // Dropdown de productos
    public $showProductoDropdown = false;
    public $selectedProducto = null;

    protected $paginationTheme = 'bootstrap';

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

    public function updatingSearchProducto()
    {
        $this->showProductoDropdown = !empty($this->searchProducto);
    }

    public function render()
    {
        $bodegas = Bodega::where('activo', true)
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('nombre', 'asc')
            ->paginate(10);

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
        $this->searchProducto = '';
        $this->selectedProducto = null;
    }

    /**
     * Obtiene los productos activos filtrados para el dropdown
     */
    public function getProductoResultsProperty()
    {
        if (empty($this->searchProducto)) {
            return [];
        }

        $search = strtolower(trim($this->searchProducto));

        return Producto::with('categoria')
            ->where('activo', true)
            ->where(function($query) use ($search) {
                $query->where(DB::raw('LOWER(id)'), 'like', "%{$search}%")
                      ->orWhere(DB::raw('LOWER(descripcion)'), 'like', "%{$search}%");
            })
            ->limit(6)
            ->get()
            ->map(function($producto) {
                return [
                    'id' => $producto->id,
                    'codigo' => $producto->id,
                    'descripcion' => $producto->descripcion,
                    'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
                ];
            })
            ->toArray();
    }

    /**
     * Selecciona un producto del dropdown
     */
    public function selectProducto($productoId)
    {
        $producto = Producto::with('categoria')->find($productoId);

        if ($producto) {
            $this->selectedProducto = [
                'id' => $producto->id,
                'codigo' => $producto->id,
                'descripcion' => $producto->descripcion,
                'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
            ];

            $this->loteProductoId = $producto->id;
            $this->searchProducto = '';
            $this->showProductoDropdown = false;
        }
    }

    /**
     * Limpia la selección de producto
     */
    public function clearProducto()
    {
        $this->selectedProducto = null;
        $this->loteProductoId = '';
        $this->searchProducto = '';
    }

    /**
     * Abre el modal para crear un nuevo lote en la bodega
     */
    public function abrirModalCrearLote($bodegaId)
    {
        $this->resetFormLote();
        $this->bodegaId = $bodegaId;
        $this->loteFechaIngreso = now()->format('Y-m-d');
        $this->showModalLote = true;
    }

    /**
     * Abre el modal para editar un lote existente
     */
    public function editarLote($loteId)
    {
        $lote = Lote::with('producto')->find($loteId);

        if ($lote) {
            $this->editingLoteId = $loteId;
            $this->bodegaId = $lote->id_bodega;
            $this->loteProductoId = $lote->id_producto;
            $this->loteCantidad = $lote->cantidad;
            $this->lotePrecioIngreso = $lote->precio_ingreso;
            $this->loteFechaIngreso = $lote->fecha_ingreso ? $lote->fecha_ingreso->format('Y-m-d') : '';
            $this->loteObservaciones = $lote->observaciones ?? '';

            // Establecer el producto seleccionado
            $this->selectedProducto = [
                'id' => $lote->producto->id,
                'codigo' => $lote->producto->id,
                'descripcion' => $lote->producto->descripcion,
                'categoria' => $lote->producto->categoria->nombre ?? 'Sin categoría',
            ];

            $this->showModalLote = true;
        }
    }

    /**
     * Guarda un lote (crear o actualizar)
     */
    public function guardarLote()
    {
        $this->validate([
            'loteProductoId' => 'required|exists:producto,id',
            'loteCantidad' => 'required|integer|min:0',
            'lotePrecioIngreso' => 'required|numeric|min:0',
            'loteFechaIngreso' => 'required|date',
        ], [
            'loteProductoId.required' => 'Debe seleccionar un producto.',
            'loteProductoId.exists' => 'El producto seleccionado no existe.',
            'loteCantidad.required' => 'La cantidad es obligatoria.',
            'loteCantidad.integer' => 'La cantidad debe ser un número entero.',
            'loteCantidad.min' => 'La cantidad debe ser mayor o igual a 0.',
            'lotePrecioIngreso.required' => 'El precio de ingreso es obligatorio.',
            'lotePrecioIngreso.numeric' => 'El precio debe ser un número.',
            'lotePrecioIngreso.min' => 'El precio debe ser mayor o igual a 0.',
            'loteFechaIngreso.required' => 'La fecha de ingreso es obligatoria.',
            'loteFechaIngreso.date' => 'Debe ingresar una fecha válida.',
        ]);

        try {
            if ($this->editingLoteId) {
                // Actualizar lote existente
                $lote = Lote::findOrFail($this->editingLoteId);
                $cantidadAnterior = $lote->cantidad;

                $lote->update([
                    'id_producto' => $this->loteProductoId,
                    'cantidad' => $this->loteCantidad,
                    'cantidad_inicial' => $lote->cantidad_inicial + ($this->loteCantidad - $cantidadAnterior),
                    'precio_ingreso' => $this->lotePrecioIngreso,
                    'fecha_ingreso' => $this->loteFechaIngreso,
                    'observaciones' => $this->loteObservaciones,
                ]);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Actualizar',
                    'descripcion' => "Lote actualizado en bodega: {$lote->bodega->nombre} - Producto: {$lote->producto->descripcion}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                session()->flash('message', 'Lote actualizado correctamente.');
            } else {
                // Crear nuevo lote
                $lote = Lote::create([
                    'id_producto' => $this->loteProductoId,
                    'id_bodega' => $this->bodegaId,
                    'cantidad' => $this->loteCantidad,
                    'cantidad_inicial' => $this->loteCantidad,
                    'precio_ingreso' => $this->lotePrecioIngreso,
                    'fecha_ingreso' => $this->loteFechaIngreso,
                    'observaciones' => $this->loteObservaciones,
                    'estado' => true,
                ]);

                $bodega = Bodega::find($this->bodegaId);
                $producto = Producto::find($this->loteProductoId);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Crear',
                    'descripcion' => "Lote creado en bodega: {$bodega->nombre} - Producto: {$producto->descripcion} - Cantidad: {$this->loteCantidad}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                session()->flash('message', 'Lote creado correctamente.');
            }

            $this->closeModalLote();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el lote: ' . $e->getMessage());
        }
    }

    /**
     * Elimina (desactiva) un lote
     */
    public function eliminarLote($loteId)
    {
        try {
            $lote = Lote::findOrFail($loteId);

            // Verificar que el lote no tenga movimientos
            if ($lote->cantidad != $lote->cantidad_inicial) {
                session()->flash('error', 'No se puede desactivar un lote que tiene movimientos de inventario.');
                return;
            }

            $lote->update(['estado' => false]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Desactivar',
                'descripcion' => "Lote desactivado en bodega: {$lote->bodega->nombre} - Producto: {$lote->producto->descripcion}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Lote desactivado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al desactivar el lote: ' . $e->getMessage());
        }
    }

    /**
     * Reactiva un lote
     */
    public function activarLote($loteId)
    {
        try {
            $lote = Lote::findOrFail($loteId);
            $lote->update(['estado' => true]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Activar',
                'descripcion' => "Lote activado en bodega: {$lote->bodega->nombre} - Producto: {$lote->producto->descripcion}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Lote activado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al activar el lote: ' . $e->getMessage());
        }
    }

    /**
     * Cierra el modal de lote
     */
    public function closeModalLote()
    {
        $this->showModalLote = false;
        $this->resetFormLote();
    }

    /**
     * Limpia los campos del formulario de lote
     */
    private function resetFormLote()
    {
        $this->editingLoteId = null;
        $this->loteProductoId = '';
        $this->loteCantidad = '';
        $this->lotePrecioIngreso = '';
        $this->loteFechaIngreso = '';
        $this->loteObservaciones = '';
        $this->selectedProducto = null;
        $this->searchProducto = '';
        $this->resetErrorBag();
    }
}
