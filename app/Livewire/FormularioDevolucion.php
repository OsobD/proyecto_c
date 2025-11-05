<?php

namespace App\Livewire;

use App\Models\Bodega;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\TarjetaResponsabilidad;
use App\Models\TipoDevolucion;
use App\Models\RazonDevolucion;
use App\Models\Devolucion;
use App\Models\DetalleDevolucion;
use App\Models\Lote;
use App\Models\TipoTransaccion;
use App\Models\Transaccion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Componente FormularioDevolucion
 *
 * Formulario flexible para registrar devoluciones de productos. Permite
 * devoluciones en múltiples direcciones: Bodega↔Bodega, Empleado→Bodega, etc.
 * Incluye campo para especificar motivo de la devolución.
 *
 * @package App\Livewire
 * @see resources/views/livewire/formulario-devolucion.blade.php
 */
class FormularioDevolucion extends Component
{
    /** @var array Listado de empleados */
    public $empleados = [];

    /** @var array Listado de bodegas */
    public $bodegas = [];

    /** @var array Listado de productos */
    public $productos = [];

    /** @var string Término de búsqueda para origen */
    public $searchOrigen = '';

    /** @var string Término de búsqueda para destino */
    public $searchDestino = '';

    /** @var string Término de búsqueda de producto */
    public $searchProducto = '';

    /** @var array|null Origen seleccionado (bodega o empleado) */
    public $selectedOrigen = null;

    /** @var array|null Destino seleccionado (bodega o empleado) */
    public $selectedDestino = null;

    /** @var bool Controla dropdown de origen */
    public $showOrigenDropdown = false;

    /** @var bool Controla dropdown de destino */
    public $showDestinoDropdown = false;

    /** @var bool Controla dropdown de productos */
    public $showProductoDropdown = false;

    /** @var array Productos agregados a la devolución */
    public $productosSeleccionados = [];

    /** @var string Motivo de la devolución */
    public $motivo = '';

    /** @var string Correlativo de la devolución */
    public $correlativo = '';

    /** @var string Tipo de devolución seleccionado */
    public $tipoDevolucion = 'normal';

    /** @var int|null ID del tipo de devolución seleccionado */
    public $selectedTipoDevolucionId = null;

    /** @var int|null ID de la razón de devolución seleccionada */
    public $selectedRazonDevolucionId = null;

    /** @var array|null Persona seleccionada (para insumos no utilizados o equipo no registrado) */
    public $selectedPersona = null;

    /** @var array|null Tarjeta de responsabilidad seleccionada (para devoluciones normales) */
    public $selectedTarjeta = null;

    /** @var array|null Bodega destino seleccionada */
    public $bodegaDestino = null;

    /**
     * Inicializa el componente cargando datos de la base de datos
     *
     * @return void
     */
    public function mount()
    {
        // Cargar bodegas activas
        $this->bodegas = Bodega::where('activo', true)
            ->select('id', 'nombre')
            ->get()
            ->toArray();

        // Cargar personas activas con sus tarjetas
        $this->empleados = Persona::where('estado', true)
            ->with('tarjetasResponsabilidad:id,id_persona,activo')
            ->select('id', 'nombres', 'apellidos')
            ->get()
            ->map(function($persona) {
                return [
                    'id' => $persona->id,
                    'nombre' => $persona->nombres . ' ' . $persona->apellidos,
                    'tiene_tarjeta' => $persona->tarjetasResponsabilidad->where('activo', true)->isNotEmpty(),
                    'tarjetas' => $persona->tarjetasResponsabilidad->where('activo', true)->pluck('id')->toArray()
                ];
            })
            ->toArray();

        // Cargar todos los productos del catálogo
        $this->productos = Producto::select('id', 'descripcion')
            ->get()
            ->map(function($producto) {
                return [
                    'id' => $producto->id,
                    'descripcion' => $producto->descripcion,
                    'precio' => 0 // Se calculará según contexto
                ];
            })
            ->toArray();

        $this->productosSeleccionados = [];
    }

    public function getOrigenResultsProperty()
    {
        $results = [];

        // Filtrar según tipo de devolución
        $search = strtolower(trim($this->searchOrigen));

        // Para devolución normal, solo mostrar personas con tarjetas
        if ($this->tipoDevolucion === 'normal') {
            foreach ($this->empleados as $empleado) {
                if ($empleado['tiene_tarjeta'] &&
                    (empty($search) || str_contains(strtolower($empleado['nombre']), $search))) {
                    $results[] = [
                        'id' => 'P' . $empleado['id'],
                        'nombre' => $empleado['nombre'] . ' (Con Tarjeta)',
                        'tipo' => 'Persona',
                        'tarjetas' => $empleado['tarjetas']
                    ];
                }
            }
        } else {
            // Para equipo no registrado e insumos no utilizados, mostrar todas las personas
            foreach ($this->empleados as $empleado) {
                if (empty($search) || str_contains(strtolower($empleado['nombre']), $search)) {
                    $etiqueta = $empleado['tiene_tarjeta'] ? 'Con Tarjeta' : 'Sin Tarjeta';
                    $results[] = [
                        'id' => 'P' . $empleado['id'],
                        'nombre' => $empleado['nombre'] . ' (' . $etiqueta . ')',
                        'tipo' => 'Persona',
                        'tarjetas' => $empleado['tarjetas'] ?? []
                    ];
                }
            }
        }

        return $results;
    }

    public function getDestinoResultsProperty()
    {
        $results = [];
        $search = strtolower(trim($this->searchDestino));

        // El destino siempre son bodegas para devoluciones
        foreach ($this->bodegas as $bodega) {
            if (empty($search) || str_contains(strtolower($bodega['nombre']), $search)) {
                $results[] = [
                    'id' => 'B' . $bodega['id'],
                    'nombre' => $bodega['nombre'],
                    'tipo' => 'Bodega'
                ];
            }
        }

        return $results;
    }

    public function selectOrigen($id, $nombre, $tipo)
    {
        $this->selectedOrigen = [
            'id' => $id,
            'nombre' => $nombre,
            'tipo' => $tipo
        ];
        $this->searchOrigen = '';
        $this->showOrigenDropdown = false;
    }

    public function selectDestino($id, $nombre, $tipo)
    {
        $this->selectedDestino = [
            'id' => $id,
            'nombre' => $nombre,
            'tipo' => $tipo
        ];
        $this->searchDestino = '';
        $this->showDestinoDropdown = false;
    }

    public function clearOrigen()
    {
        $this->selectedOrigen = null;
        $this->searchOrigen = '';
        $this->showOrigenDropdown = false;
    }

    public function clearDestino()
    {
        $this->selectedDestino = null;
        $this->searchDestino = '';
        $this->showDestinoDropdown = false;
    }

    public function updatedSearchOrigen()
    {
        $this->showOrigenDropdown = true;
    }

    public function updatedSearchDestino()
    {
        $this->showDestinoDropdown = true;
    }

    public function updatedSearchProducto()
    {
        $this->showProductoDropdown = true;
    }

    public function seleccionarPrimerResultado()
    {
        $resultados = $this->productoResults;
        if (!empty($resultados)) {
            $primerProducto = array_values($resultados)[0];
            $this->selectProducto($primerProducto['id']);
        }
    }

    public function getProductoResultsProperty()
    {
        $search = strtolower(trim($this->searchProducto));
        $productosFiltrados = [];

        // Si es equipo no registrado, mostrar todos los productos
        if ($this->tipoDevolucion === 'equipo_no_registrado') {
            $productosFiltrados = Producto::when(!empty($search), function($query) use ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('descripcion', 'LIKE', "%{$search}%")
                          ->orWhere('id', 'LIKE', "%{$search}%");
                    });
                })
                ->select('id', 'descripcion')
                ->limit(20)
                ->get()
                ->map(function($producto) {
                    return [
                        'id' => $producto->id,
                        'descripcion' => $producto->descripcion,
                        'precio' => 0 // Usuario lo ingresa manualmente
                    ];
                })
                ->toArray();
        } else {
            // Para devolución normal e insumos no utilizados
            // Mostrar solo productos con lotes activos en alguna bodega
            // O productos asignados a la persona/tarjeta si hay origen seleccionado

            if ($this->selectedOrigen && isset($this->selectedOrigen['tarjetas']) && !empty($this->selectedOrigen['tarjetas'])) {
                // Filtrar por productos en las tarjetas de la persona
                $productosFiltrados = DB::table('tarjeta_producto as tp')
                    ->join('producto as p', 'tp.id_producto', '=', 'p.id')
                    ->join('lote as l', 'tp.id_lote', '=', 'l.id')
                    ->whereIn('tp.id_tarjeta', $this->selectedOrigen['tarjetas'])
                    ->where('l.estado', true)
                    ->where('l.cantidad', '>', 0)
                    ->when(!empty($search), function($query) use ($search) {
                        $query->where(function($q) use ($search) {
                            $q->where('p.descripcion', 'LIKE', "%{$search}%")
                              ->orWhere('p.id', 'LIKE', "%{$search}%");
                        });
                    })
                    ->select('p.id', 'p.descripcion', DB::raw('AVG(l.precio_ingreso) as precio'))
                    ->groupBy('p.id', 'p.descripcion')
                    ->limit(20)
                    ->get()
                    ->map(function($producto) {
                        return [
                            'id' => $producto->id,
                            'descripcion' => $producto->descripcion,
                            'precio' => $producto->precio ?? 0
                        ];
                    })
                    ->toArray();
            } else {
                // Mostrar productos con lotes activos
                $productosFiltrados = Producto::whereHas('lotes', function($query) {
                        $query->where('estado', true)
                              ->where('cantidad', '>', 0);
                    })
                    ->when(!empty($search), function($query) use ($search) {
                        $query->where(function($q) use ($search) {
                            $q->where('descripcion', 'LIKE', "%{$search}%")
                              ->orWhere('id', 'LIKE', "%{$search}%");
                        });
                    })
                    ->select('id', 'descripcion')
                    ->limit(20)
                    ->get()
                    ->map(function($producto) {
                        // Calcular precio promedio de lotes activos
                        $precioPromedio = Lote::where('id_producto', $producto->id)
                            ->where('estado', true)
                            ->where('cantidad', '>', 0)
                            ->avg('precio_ingreso');

                        return [
                            'id' => $producto->id,
                            'descripcion' => $producto->descripcion,
                            'precio' => $precioPromedio ?? 0
                        ];
                    })
                    ->toArray();
            }
        }

        return $productosFiltrados;
    }

    public function selectProducto($productoId)
    {
        // Buscar en los resultados filtrados
        $producto = collect($this->productoResults)->firstWhere('id', $productoId);

        if ($producto && !collect($this->productosSeleccionados)->contains('id', $producto['id'])) {
            $this->productosSeleccionados[] = [
                'id' => $producto['id'],
                'descripcion' => $producto['descripcion'],
                'precio' => $producto['precio'] ?? 0,
                'cantidad' => 1,
                'estado' => 'bueno' // Valor por defecto
            ];
        }
        $this->searchProducto = '';
        $this->showProductoDropdown = false;
    }

    public function eliminarProducto($productoId)
    {
        $this->productosSeleccionados = array_filter($this->productosSeleccionados, function($item) use ($productoId) {
            return $item['id'] != $productoId; // Use != for loose comparison to handle both strings and ints
        });
        $this->productosSeleccionados = array_values($this->productosSeleccionados);
    }

    public function actualizarCantidad($productoId, $cantidad)
    {
        foreach ($this->productosSeleccionados as &$producto) {
            if ($producto['id'] == $productoId) { // Use == for loose comparison
                $producto['cantidad'] = max(1, (int)$cantidad);
                break;
            }
        }
    }

    public function getSubtotalProperty()
    {
        return collect($this->productosSeleccionados)->sum(function($producto) {
            return $producto['cantidad'] * $producto['precio'];
        });
    }

    /**
     * Actualiza el estado de un producto seleccionado
     */
    public function actualizarEstado($productoId, $estado)
    {
        foreach ($this->productosSeleccionados as &$producto) {
            if ($producto['id'] == $productoId) { // Use == for loose comparison
                $producto['estado'] = $estado;
                break;
            }
        }
    }

    /**
     * Actualiza el precio de un producto (solo para equipo no registrado)
     */
    public function actualizarPrecio($productoId, $precio)
    {
        foreach ($this->productosSeleccionados as &$producto) {
            if ($producto['id'] == $productoId) { // Use == for loose comparison
                $producto['precio'] = max(0, (float)$precio);
                break;
            }
        }
    }

    /**
     * Obtiene los tipos de devolución disponibles
     */
    public function getTiposDevolucionProperty()
    {
        return TipoDevolucion::select('id', 'nombre')
            ->get()
            ->map(function($tipo) {
                $value = match($tipo->nombre) {
                    'Normal' => 'normal',
                    'Equipo No Registrado' => 'equipo_no_registrado',
                    'Insumos No Utilizados' => 'insumos_no_utilizados',
                    default => strtolower(str_replace(' ', '_', $tipo->nombre))
                };

                return [
                    'id' => $tipo->id,
                    'nombre' => $tipo->nombre,
                    'value' => $value
                ];
            })
            ->toArray();
    }

    /**
     * Obtiene las razones de devolución disponibles
     */
    public function getRazonesDevolucionProperty()
    {
        return RazonDevolucion::select('id', 'nombre')
            ->get()
            ->toArray();
    }

    /**
     * Guarda la devolución en la base de datos
     */
    public function save()
    {
        // Validaciones
        $this->validate([
            'selectedRazonDevolucionId' => 'required|exists:razon_devolucion,id',
            'selectedDestino' => 'required',
            'selectedOrigen' => 'required',
            'productosSeleccionados' => 'required|min:1',
            'correlativo' => 'nullable|string|max:255',
            'motivo' => 'nullable|string|max:1000',
        ], [
            'selectedRazonDevolucionId.required' => 'Debe seleccionar una razón de devolución',
            'selectedDestino.required' => 'Debe seleccionar una bodega de destino',
            'selectedOrigen.required' => 'Debe seleccionar el origen de la devolución',
            'productosSeleccionados.required' => 'Debe agregar al menos un producto',
            'productosSeleccionados.min' => 'Debe agregar al menos un producto',
        ]);

        // Validar precios en equipo no registrado
        if ($this->tipoDevolucion === 'equipo_no_registrado') {
            foreach ($this->productosSeleccionados as $producto) {
                if ($producto['precio'] <= 0) {
                    session()->flash('error', 'Todos los productos deben tener un precio válido para equipo no registrado');
                    return;
                }
            }
        }

        DB::beginTransaction();
        try {
            // Obtener bodega destino
            $bodegaId = (int)str_replace('B', '', $this->selectedDestino['id']);

            // Obtener tipo de devolución
            $tipoDevolucion = TipoDevolucion::where('nombre', match($this->tipoDevolucion) {
                'normal' => 'Normal',
                'equipo_no_registrado' => 'Equipo No Registrado',
                'insumos_no_utilizados' => 'Insumos No Utilizados',
            })->first();

            // Calcular total
            $total = $this->subtotal;

            // Crear devolución
            $devolucion = Devolucion::create([
                'fecha' => now(),
                'no_formulario' => $this->correlativo,
                'total' => $total,
                'id_usuario' => Auth::id(),
                'id_tarjeta' => null, // Se maneja en detalles
                'id_bodega' => $bodegaId,
                'id_tipo_devolucion' => $tipoDevolucion->id,
                'id_razon_devolucion' => $this->selectedRazonDevolucionId,
            ]);

            // Procesar productos según tipo de devolución
            foreach ($this->productosSeleccionados as $producto) {
                $this->procesarProductoDevolucion($devolucion, $producto, $bodegaId);
            }

            // Crear transacción
            $tipoTransaccion = TipoTransaccion::firstOrCreate(['nombre' => 'Devolución']);
            Transaccion::create([
                'fecha' => now(),
                'descripcion' => 'Devolución de material - ' . $tipoDevolucion->nombre,
                'id_tipo_transaccion' => $tipoTransaccion->id,
                'id_devolucion' => $devolucion->id,
            ]);

            DB::commit();

            session()->flash('success', 'Devolución registrada exitosamente');
            return redirect()->route('devoluciones');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al registrar la devolución: ' . $e->getMessage());
        }
    }

    /**
     * Procesa un producto de la devolución según el tipo
     */
    private function procesarProductoDevolucion($devolucion, $producto, $bodegaId)
    {
        if ($this->tipoDevolucion === 'equipo_no_registrado') {
            // Equipo no registrado
            $this->procesarEquipoNoRegistrado($devolucion, $producto, $bodegaId);
        } else {
            // Devolución normal o insumos no utilizados
            $this->procesarDevolucionNormal($devolucion, $producto, $bodegaId);
        }
    }

    /**
     * Procesa equipo no registrado
     */
    private function procesarEquipoNoRegistrado($devolucion, $producto, $bodegaId)
    {
        $idLote = null;

        // Solo crear/usar lote si está en buen estado
        if ($producto['estado'] === 'bueno') {
            // Buscar lote de ajuste para esta bodega
            $loteAjuste = Lote::obtenerLoteAjuste($bodegaId);

            if ($loteAjuste) {
                // Incrementar cantidad del lote de ajuste
                $loteAjuste->cantidad += $producto['cantidad'];
                $loteAjuste->cantidad_inicial += $producto['cantidad'];
                $loteAjuste->save();
                $idLote = $loteAjuste->id;
            } else {
                // Crear nuevo lote para el producto
                $tipoTransaccion = TipoTransaccion::firstOrCreate(['nombre' => 'Entrada']);
                $transaccion = Transaccion::create([
                    'fecha' => now(),
                    'descripcion' => 'Lote creado por devolución de equipo no registrado',
                    'id_tipo_transaccion' => $tipoTransaccion->id,
                ]);

                $lote = Lote::create([
                    'cantidad' => $producto['cantidad'],
                    'cantidad_inicial' => $producto['cantidad'],
                    'fecha_ingreso' => now(),
                    'precio_ingreso' => $producto['precio'],
                    'observaciones' => 'Lote creado por devolución de equipo no registrado',
                    'id_producto' => $producto['id'],
                    'id_bodega' => $bodegaId,
                    'estado' => true,
                    'id_transaccion' => $transaccion->id,
                ]);
                $idLote = $lote->id;
            }
        }
        // Si está en mal estado, idLote queda null

        // Crear detalle de devolución
        DetalleDevolucion::create([
            'id_devolucion' => $devolucion->id,
            'id_producto' => $producto['id'],
            'id_lote' => $idLote,
            'cantidad' => $producto['cantidad'],
            'estado_producto' => $producto['estado'],
            'precio_unitario' => $producto['precio'],
        ]);
    }

    /**
     * Procesa devolución normal o insumos no utilizados
     */
    private function procesarDevolucionNormal($devolucion, $producto, $bodegaId)
    {
        // Buscar lote del producto en la bodega destino
        // Aplicar PEPS: el lote más antiguo primero
        $lote = Lote::where('id_producto', $producto['id'])
            ->where('id_bodega', $bodegaId)
            ->orderBy('fecha_ingreso', 'asc')
            ->first();

        if ($lote) {
            // Reactivar lote si está inactivo
            if (!$lote->estado) {
                $lote->estado = true;
            }

            // Sumar cantidad devuelta
            $lote->cantidad += $producto['cantidad'];
            $lote->save();

            $idLote = $lote->id;
            $precioUnitario = $lote->precio_ingreso;
        } else {
            // Si no existe lote, crear uno nuevo (caso de primera devolución)
            $tipoTransaccion = TipoTransaccion::firstOrCreate(['nombre' => 'Devolución']);
            $transaccion = Transaccion::create([
                'fecha' => now(),
                'descripcion' => 'Lote creado por devolución',
                'id_tipo_transaccion' => $tipoTransaccion->id,
            ]);

            $lote = Lote::create([
                'cantidad' => $producto['cantidad'],
                'cantidad_inicial' => $producto['cantidad'],
                'fecha_ingreso' => now(),
                'precio_ingreso' => $producto['precio'],
                'observaciones' => 'Lote creado por devolución',
                'id_producto' => $producto['id'],
                'id_bodega' => $bodegaId,
                'estado' => true,
                'id_transaccion' => $transaccion->id,
            ]);

            $idLote = $lote->id;
            $precioUnitario = $producto['precio'];
        }

        // Crear detalle de devolución
        DetalleDevolucion::create([
            'id_devolucion' => $devolucion->id,
            'id_producto' => $producto['id'],
            'id_lote' => $idLote,
            'cantidad' => $producto['cantidad'],
            'estado_producto' => $producto['estado'],
            'precio_unitario' => $precioUnitario,
        ]);
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.formulario-devolucion');
    }
}
