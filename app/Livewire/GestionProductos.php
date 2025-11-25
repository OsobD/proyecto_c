<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Lote;
use App\Models\Bitacora;
use App\Models\Bodega;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Componente GestionProductos
 *
 * Gestiona el CRUD completo de productos del sistema de inventario.
 * Permite crear, editar, buscar, activar/desactivar productos y visualizar
 * sus lotes existentes con operaciones CRUD sobre los mismos.
 *
 * **Funcionalidades principales:**
 * - Listado de productos con búsqueda en tiempo real
 * - Creación y edición de productos mediante modal
 * - Asociación de productos con categorías
 * - Creación rápida de categorías desde el mismo formulario (sub-modal)
 * - Activación/desactivación de productos (soft delete)
 * - Visualización de lotes por producto en modal dedicado
 * - CRUD completo de lotes (crear con modal, editar inline, activar/desactivar)
 *
 * @package App\Livewire
 * @version 2.0
 * @see resources/views/livewire/gestion-productos.blade.php Vista asociada
 */
class GestionProductos extends Component
{
    use WithPagination;

    // Propiedades de búsqueda y filtrado
    /** @var string Término de búsqueda para filtrar productos */
    public $searchProducto = '';

    // Modal de filtros
    public $showFilterModal = false;
    public $showInactive = false;

    // Ordenamiento
    public $sortField = 'id';
    public $sortDirection = 'asc';

    /** @var int Número de elementos por página en la lista principal */
    protected $perPage = 30;

    /** @var int Página actual de lotes en el modal */
    public $lotesPage = 1;

    /** @var int Número de lotes por página en el modal */
    protected $lotesPerPage = 10;

    // Propiedades de control de UI
    /** @var bool Controla visibilidad del modal de producto */
    public $showModal = false;

    /** @var bool Controla visibilidad del sub-modal de categoría */
    public $showSubModalCategoria = false;

    /** @var bool Controla visibilidad del modal de crear lote */
    public $showModalLotes = false;

    /** @var string|null ID del producto cuyos lotes están expandidos */
    public $productoIdLotesExpandido = null;

    /** @var string|null ID del producto en edición (null = modo creación) */
    public $editingId = null;

    /** @var int|null ID del lote en edición */
    public $editingLoteId = null;

    // Campos del formulario de producto
    /** @var string Código único del producto */
    public $codigo = '';

    /** @var string Descripción del producto */
    public $descripcion = '';

    /** @var string|int ID de la categoría seleccionada */
    public $categoriaId = '';

    /** @var bool Indica si el producto es consumible */
    public $esConsumible = false;

    // Campo para crear categoría
    /** @var string Nombre de nueva categoría a crear */
    public $nuevaCategoriaNombre = '';

    // Campos del formulario de lote
    /** @var int Cantidad del lote */
    public $loteCantidad = '';

    /** @var float Precio de ingreso del lote */
    public $lotePrecioIngreso = '';

    /** @var string Fecha de ingreso del lote */
    public $loteFechaIngreso = '';

    /** @var int ID de la bodega del lote */
    public $loteBodegaId = '';

    /** @var string Observaciones del lote */
    public $loteObservaciones = '';

    /** @var string ID del producto al que pertenece el lote */
    public $loteProductoId = '';

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
        $this->sortField = 'id';
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
        // Cargar productos con sus relaciones - PAGINADO
        $query = Producto::with(['categoria'])->withCount('lotes');

        // Filtrar por estado
        if (!$this->showInactive) {
            $query->where('activo', true);
        }

        // Aplicar búsqueda
        if ($this->searchProducto) {
            $search = strtolower(trim($this->searchProducto));
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw('LOWER(id)'), 'like', "%{$search}%")
                    ->orWhere(DB::raw('LOWER(descripcion)'), 'like', "%{$search}%")
                    ->orWhereHas('categoria', function ($subQ) use ($search) {
                        $subQ->where(DB::raw('LOWER(nombre)'), 'like', "%{$search}%");
                    });
            });
        }

        // Aplicar ordenamiento
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('descripcion');
        }

        $productos = $query->paginate(10);

        // Obtener lotes paginados si hay un producto expandido
        $lotesPaginados = null;
        if ($this->productoIdLotesExpandido) {
            $lotesPaginados = Lote::where('id_producto', $this->productoIdLotesExpandido)
                ->with('bodega')
                ->orderBy('fecha_ingreso', 'desc')
                ->paginate($this->lotesPerPage, ['*'], 'lotesPage', $this->lotesPage);
        }

        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $bodegas = Bodega::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('livewire.gestion-productos', [
            'productos' => $productos,
            'lotesPaginados' => $lotesPaginados,
            'categorias' => $categorias,
            'bodegas' => $bodegas,
        ]);
    }

    /**
     * Abre el modal de producto en modo creación
     *
     * @return void
     */
    public function abrirModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Abre el modal de producto en modo edición
     *
     * Carga los datos del producto seleccionado en el formulario.
     *
     * @param string $id ID del producto a editar
     * @return void
     */
    public function editarProducto($id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            $this->editingId = $id;
            $this->codigo = $producto->id;
            $this->descripcion = $producto->descripcion;
            $this->categoriaId = $producto->id_categoria;
            $this->esConsumible = $producto->es_consumible;
            $this->showModal = true;
        }
    }

    /**
     * Guarda un producto (crear o actualizar según editingId)
     *
     * Valida los campos del formulario y persiste los cambios.
     * Muestra mensaje de éxito mediante flash session.
     *
     * @return void
     */
    public function guardarProducto()
    {
        $rules = [
            'codigo' => 'required|min:1|max:50',
            'descripcion' => 'required|min:3|max:255',
            'categoriaId' => 'required|exists:categoria,id',
        ];

        // Si estamos creando, validar que el código no exista
        if (!$this->editingId) {
            $rules['codigo'] .= '|unique:producto,id';
        }

        $this->validate($rules, [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.min' => 'El código debe tener al menos 1 carácter.',
            'codigo.unique' => 'Este código de producto ya existe.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 3 caracteres.',
            'categoriaId.required' => 'Debe seleccionar una categoría.',
            'categoriaId.exists' => 'La categoría seleccionada no existe.',
        ]);

        if ($this->editingId) {
            // Actualizar producto existente
            $producto = Producto::find($this->editingId);
            if ($producto) {
                $producto->descripcion = $this->descripcion;
                $producto->id_categoria = $this->categoriaId;
                $producto->es_consumible = $this->esConsumible;
                $producto->save();

                $producto->save();

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Actualizar',
                    'modelo' => 'Producto',
                    'modelo_id' => $producto->id,
                    'descripcion' => "Producto actualizado: {$producto->descripcion}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                session()->flash('message', 'Producto actualizado exitosamente.');
            }
        } else {
            // Crear nuevo producto
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
                'modelo' => 'Producto',
                'modelo_id' => $this->codigo,
                'descripcion' => "Producto creado: {$this->descripcion}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Producto creado exitosamente.');
        }

        $this->closeModal();
    }

    /**
     * Cambia el estado activo/inactivo de un producto (soft delete)
     *
     * @param string $id ID del producto a activar/desactivar
     * @return void
     */
    public function toggleEstado($id)
    {
        $producto = Producto::find($id);
        if ($producto) {
            $producto->activo = !$producto->activo;
            $producto->save();

            $producto->save();

            // Registrar en bitácora
            Bitacora::create([
                'accion' => $producto->activo ? 'Activar' : 'Desactivar',
                'modelo' => 'Producto',
                'modelo_id' => $producto->id,
                'descripcion' => "Producto " . ($producto->activo ? 'activado' : 'desactivado') . ": {$producto->descripcion}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Estado del producto actualizado.');
        }
    }

    /**
     * Expande/colapsa los lotes de un producto
     *
     * @param string $id ID del producto cuyos lotes se desean ver
     * @return void
     */
    public function toggleLotes($id)
    {
        if ($this->productoIdLotesExpandido === $id) {
            $this->productoIdLotesExpandido = null;
            $this->lotesPage = 1;
        } else {
            $this->productoIdLotesExpandido = $id;
            $this->lotesPage = 1; // Resetear a la primera página al abrir un nuevo producto
        }
    }

    /**
     * Cambia de página en la paginación de lotes
     *
     * @param int $page Número de página
     * @return void
     */
    public function goToLotesPage($page)
    {
        $this->lotesPage = $page;
    }

    /**
     * Resetea la paginación cuando cambia la búsqueda
     *
     * @return void
     */
    public function updatingSearchProducto()
    {
        $this->resetPage();
    }

    /**
     * Abre el sub-modal para crear nueva categoría
     *
     * @return void
     */
    public function abrirSubModalCategoria()
    {
        $this->nuevaCategoriaNombre = '';
        $this->showSubModalCategoria = true;
    }

    /**
     * Guarda una nueva categoría desde el sub-modal
     *
     * Al crear exitosamente, selecciona automáticamente la nueva categoría
     * en el formulario principal de producto.
     *
     * @return void
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

        // Registrar en bitácora
        Bitacora::create([
            'accion' => 'Crear',
            'modelo' => 'Categoria',
            'modelo_id' => $categoria->id,
            'descripcion' => "Categoría creada desde productos: {$categoria->nombre}",
            'id_usuario' => Auth::id(),
            'created_at' => now(),
        ]);

        $this->categoriaId = $categoria->id;
        $this->showSubModalCategoria = false;
        $this->nuevaCategoriaNombre = '';
    }

    /**
     * Cierra el sub-modal de categoría
     *
     * @return void
     */
    public function closeSubModalCategoria()
    {
        $this->showSubModalCategoria = false;
        $this->nuevaCategoriaNombre = '';
    }

    /**
     * Cierra el modal principal de producto
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
        $this->codigo = '';
        $this->descripcion = '';
        $this->categoriaId = '';
        $this->esConsumible = false;
        $this->resetErrorBag();
    }

    // ==================== MÉTODOS PARA GESTIÓN DE LOTES ====================

    /**
     * Abre el modal para crear un nuevo lote
     *
     * @param string $productoId ID del producto al que pertenecerá el lote
     * @return void
     */
    public function abrirModalCrearLote($productoId)
    {
        $this->resetFormLote();
        $this->loteProductoId = $productoId;
        $this->loteFechaIngreso = now()->format('Y-m-d');
        $this->showModalLotes = true;
    }

    /**
     * Activa el modo de edición inline para un lote
     *
     * @param int $loteId ID del lote a editar
     * @return void
     */
    public function editarLote($loteId)
    {
        $lote = Lote::find($loteId);

        if ($lote) {
            $this->editingLoteId = $loteId;
            $this->loteProductoId = $lote->id_producto;
            $this->loteCantidad = $lote->cantidad;
            $this->lotePrecioIngreso = $lote->precio_ingreso;
            $this->loteFechaIngreso = $lote->fecha_ingreso ? date('Y-m-d', strtotime($lote->fecha_ingreso)) : '';
            $this->loteBodegaId = $lote->id_bodega;
            $this->loteObservaciones = $lote->observaciones ?? '';
        }
    }

    /**
     * Cancela la edición inline de un lote
     *
     * @return void
     */
    public function cancelarEdicionLote()
    {
        $this->editingLoteId = null;
        $this->resetFormLote();
    }

    /**
     * Guarda un lote (crear o actualizar según editingLoteId)
     *
     * @return void
     */
    public function guardarLote()
    {
        $this->validate([
            'loteCantidad' => 'required|integer|min:0',
            'lotePrecioIngreso' => 'required|numeric|min:0',
            'loteFechaIngreso' => 'required|date',
            'loteBodegaId' => 'required|exists:bodega,id',
        ], [
            'loteCantidad.required' => 'La cantidad es obligatoria.',
            'loteCantidad.integer' => 'La cantidad debe ser un número entero.',
            'loteCantidad.min' => 'La cantidad debe ser mayor o igual a 0.',
            'lotePrecioIngreso.required' => 'El precio de ingreso es obligatorio.',
            'lotePrecioIngreso.numeric' => 'El precio debe ser un número.',
            'lotePrecioIngreso.min' => 'El precio debe ser mayor o igual a 0.',
            'loteFechaIngreso.required' => 'La fecha de ingreso es obligatoria.',
            'loteFechaIngreso.date' => 'Debe ingresar una fecha válida.',
            'loteBodegaId.required' => 'Debe seleccionar una bodega.',
            'loteBodegaId.exists' => 'La bodega seleccionada no existe.',
        ]);

        // Detectar si estamos editando o creando ANTES de hacer cambios
        $esEdicion = $this->editingLoteId !== null;

        if ($esEdicion) {
            // Actualizar lote existente (edición inline)
            $lote = Lote::find($this->editingLoteId);
            if ($lote) {
                $cantidadAnterior = $lote->cantidad_disponible;
                $bodegaAnterior = $lote->id_bodega;

                // Actualizar cantidad disponible
                $lote->cantidad_disponible = $this->loteCantidad;

                // Ajustar cantidad_inicial si cambió la cantidad
                if ($cantidadAnterior != $this->loteCantidad) {
                    $diferencia = $this->loteCantidad - $cantidadAnterior;
                    $lote->cantidad_inicial = $lote->cantidad_inicial + $diferencia;

                    // Actualizar también en lote_bodega si la bodega no cambió
                    if ($bodegaAnterior == $this->loteBodegaId) {
                        $ubicacion = $lote->obtenerUbicacion($this->loteBodegaId);
                        $ubicacion->cantidad += $diferencia;
                        $ubicacion->save();
                    }
                }

                // Si cambió la bodega, mover el lote
                if ($bodegaAnterior != $this->loteBodegaId) {
                    // Mover toda la cantidad a la nueva bodega
                    $ubicacionAnterior = $lote->ubicaciones()->where('id_bodega', $bodegaAnterior)->first();
                    if ($ubicacionAnterior) {
                        $cantidadAMover = $ubicacionAnterior->cantidad;
                        $ubicacionAnterior->cantidad = 0;
                        $ubicacionAnterior->save();

                        $ubicacionNueva = $lote->obtenerUbicacion($this->loteBodegaId);
                        $ubicacionNueva->cantidad += $cantidadAMover;
                        $ubicacionNueva->save();
                    }
                }

                $lote->precio_ingreso = $this->lotePrecioIngreso;
                $lote->fecha_ingreso = $this->loteFechaIngreso;
                $lote->id_bodega = $this->loteBodegaId;  // Mantenido para compatibilidad
                $lote->observaciones = $this->loteObservaciones;
                $lote->save();

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Actualizar',
                    'modelo' => 'Lote',
                    'modelo_id' => $lote->id,
                    'descripcion' => "Lote actualizado para producto: {$lote->id_producto}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                session()->flash('message', 'Lote actualizado exitosamente.');
            }
        } else {
            // Crear nuevo lote desde modal (independiente de bodega)
            $lote = Lote::create([
                'id_producto' => $this->loteProductoId,
                'cantidad_disponible' => $this->loteCantidad,
                'cantidad_inicial' => $this->loteCantidad,
                'precio_ingreso' => $this->lotePrecioIngreso,
                'fecha_ingreso' => $this->loteFechaIngreso,
                'id_bodega' => $this->loteBodegaId,  // Mantenido temporalmente para compatibilidad
                'observaciones' => $this->loteObservaciones,
                'estado' => true,
            ]);

            // Registrar ubicación del lote en la bodega especificada
            $lote->incrementarEnBodega($this->loteBodegaId, $this->loteCantidad);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Crear',
                'modelo' => 'Lote',
                'modelo_id' => $lote->id,
                'descripcion' => "Lote creado para producto: {$this->loteProductoId}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Lote creado exitosamente.');
        }

        // Si estábamos editando inline, solo salir del modo edición
        // Si estábamos creando desde modal, cerrar el modal
        if ($esEdicion) {
            $this->editingLoteId = null;
            $this->resetFormLote();
        } else {
            $this->showModalLotes = false;
            $this->resetFormLote();
        }
    }

    /**
     * Elimina (soft delete) un lote
     *
     * @param int $loteId ID del lote a eliminar
     * @return void
     */
    public function eliminarLote($loteId)
    {
        $lote = Lote::find($loteId);
        if ($lote) {
            // Verificar que el lote no tenga movimientos (cantidad == cantidad_inicial)
            if ($lote->cantidad == $lote->cantidad_inicial) {
                $lote->estado = false;
                $lote->save();
                $lote->save();

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Desactivar',
                    'modelo' => 'Lote',
                    'modelo_id' => $lote->id,
                    'descripcion' => "Lote desactivado: {$lote->id}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                session()->flash('message', 'Lote desactivado exitosamente.');
            } else {
                session()->flash('error', 'No se puede desactivar un lote que tiene movimientos de inventario.');
            }
        }
    }

    /**
     * Reactiva un lote
     *
     * @param int $loteId ID del lote a reactivar
     * @return void
     */
    public function activarLote($loteId)
    {
        $lote = Lote::find($loteId);
        if ($lote) {
            $lote->estado = true;
            $lote->save();
            $lote->save();

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Activar',
                'modelo' => 'Lote',
                'modelo_id' => $lote->id,
                'descripcion' => "Lote activado: {$lote->id}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Lote activado exitosamente.');
        }
    }

    /**
     * Cierra el modal de crear lote
     *
     * @return void
     */
    public function closeModalLotes()
    {
        $this->showModalLotes = false;
        $this->resetFormLote();
    }

    /**
     * Limpia los campos del formulario de lote
     *
     * @return void
     */
    private function resetFormLote()
    {
        $this->editingLoteId = null;
        $this->loteProductoId = '';
        $this->loteCantidad = '';
        $this->lotePrecioIngreso = '';
        $this->loteFechaIngreso = '';
        $this->loteBodegaId = '';
        $this->loteObservaciones = '';
        $this->resetErrorBag();
    }
}
