<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Lote;
use App\Models\Bodega;
use Illuminate\Support\Facades\DB;

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
 * - Visualización de lotes por producto al hacer clic en el nombre
 * - CRUD completo de lotes (crear, editar, eliminar)
 *
 * @package App\Livewire
 * @version 2.0
 * @see resources/views/livewire/gestion-productos.blade.php Vista asociada
 */
class GestionProductos extends Component
{
    // Propiedades de búsqueda y filtrado
    /** @var string Término de búsqueda para filtrar productos */
    public $searchProducto = '';

    // Propiedades de control de UI
    /** @var bool Controla visibilidad del modal de producto */
    public $showModal = false;

    /** @var bool Controla visibilidad del sub-modal de categoría */
    public $showSubModalCategoria = false;

    /** @var bool Controla visibilidad del modal de lotes */
    public $showModalLotes = false;

    /** @var bool Controla visibilidad del modal de edición de lote */
    public $showModalEditarLote = false;

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

    /**
     * Renderiza la vista del componente con datos desde BD
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Cargar productos con sus relaciones
        $productos = Producto::with(['categoria', 'lotes.bodega'])
            ->when($this->searchProducto, function($query) {
                $search = strtolower(trim($this->searchProducto));
                $query->where(function($q) use ($search) {
                    $q->where(DB::raw('LOWER(id)'), 'like', "%{$search}%")
                      ->orWhere(DB::raw('LOWER(descripcion)'), 'like', "%{$search}%")
                      ->orWhereHas('categoria', function($subQ) use ($search) {
                          $subQ->where(DB::raw('LOWER(nombre)'), 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('descripcion')
            ->get();

        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $bodegas = Bodega::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('livewire.gestion-productos', [
            'productos' => $productos,
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
        $this->productoIdLotesExpandido = $this->productoIdLotesExpandido === $id ? null : $id;
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
     * Abre el modal para editar un lote existente
     *
     * @param int $loteId ID del lote a editar
     * @return void
     */
    public function editarLote($loteId)
    {
        $lote = Lote::find($loteId);

        if ($lote) {
            // Primero cerramos cualquier modal abierto
            $this->showModalLotes = false;
            $this->showModalEditarLote = false;
            $this->resetFormLote();

            // Luego cargamos los datos del lote
            $this->editingLoteId = $loteId;
            $this->loteProductoId = $lote->id_producto;
            $this->loteCantidad = $lote->cantidad;
            $this->lotePrecioIngreso = $lote->precio_ingreso;
            $this->loteFechaIngreso = $lote->fecha_ingreso ? date('Y-m-d', strtotime($lote->fecha_ingreso)) : '';
            $this->loteBodegaId = $lote->id_bodega;
            $this->loteObservaciones = $lote->observaciones ?? '';

            // Finalmente abrimos el modal de edición
            $this->showModalEditarLote = true;
        }
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

        // Guardar el ID del producto para volver a abrir su modal después
        $productoId = $this->loteProductoId;

        if ($this->editingLoteId) {
            // Actualizar lote existente
            $lote = Lote::find($this->editingLoteId);
            if ($lote) {
                $cantidadAnterior = $lote->cantidad;
                $lote->cantidad = $this->loteCantidad;
                // Ajustar cantidad_inicial si cambió la cantidad
                if ($cantidadAnterior != $this->loteCantidad) {
                    $diferencia = $this->loteCantidad - $cantidadAnterior;
                    $lote->cantidad_inicial = $lote->cantidad_inicial + $diferencia;
                }
                $lote->precio_ingreso = $this->lotePrecioIngreso;
                $lote->fecha_ingreso = $this->loteFechaIngreso;
                $lote->id_bodega = $this->loteBodegaId;
                $lote->observaciones = $this->loteObservaciones;
                $lote->save();

                session()->flash('message', 'Lote actualizado exitosamente.');
            }
        } else {
            // Crear nuevo lote
            Lote::create([
                'id_producto' => $this->loteProductoId,
                'cantidad' => $this->loteCantidad,
                'cantidad_inicial' => $this->loteCantidad,
                'precio_ingreso' => $this->lotePrecioIngreso,
                'fecha_ingreso' => $this->loteFechaIngreso,
                'id_bodega' => $this->loteBodegaId,
                'observaciones' => $this->loteObservaciones,
                'estado' => true,
            ]);

            session()->flash('message', 'Lote creado exitosamente.');
        }

        // Cerrar el modal de edición y volver a abrir el modal de visualización de lotes
        $this->closeModalLotes();
        $this->productoIdLotesExpandido = $productoId;
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
            session()->flash('message', 'Lote activado exitosamente.');
        }
    }

    /**
     * Cierra el modal de lotes
     *
     * @return void
     */
    public function closeModalLotes()
    {
        $this->showModalLotes = false;
        $this->showModalEditarLote = false;
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
