<?php

namespace App\Livewire;

use App\Models\Bodega;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\TarjetaResponsabilidad;
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

    /** @var string Número de serie de la devolución */
    public $no_serie = '';

    /** @var bool Controla modal de confirmación */
    public $showModalConfirmacion = false;

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
        $search = strtolower(trim($this->searchOrigen));

        // Mostrar todas las personas con sus tarjetas
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

    public function selectOrigen($id, $nombre, $tipo, $tarjetas = [])
    {
        $this->selectedOrigen = [
            'id' => $id,
            'nombre' => $nombre,
            'tipo' => $tipo,
            'tarjetas' => $tarjetas
        ];
        $this->searchOrigen = '';
        $this->showOrigenDropdown = false;

        // Limpiar productos seleccionados cuando cambia el origen
        $this->productosSeleccionados = [];
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

        if (!$this->selectedOrigen) {
            return [];
        }

        if (isset($this->selectedOrigen['tarjetas']) && !empty($this->selectedOrigen['tarjetas'])) {
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
                'cantidad' => 1
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
     * Guarda la devolución en la base de datos
     */
    public function save()
    {
        // Validaciones
        $this->validate([
            'selectedDestino' => 'required',
            'selectedOrigen' => 'required',
            'productosSeleccionados' => 'required|min:1',
            'correlativo' => 'nullable|string|max:255',
            'no_serie' => 'nullable|string|max:255',
            'motivo' => 'nullable|string|max:1000',
        ], [
            'selectedDestino.required' => 'Debe seleccionar una bodega de destino',
            'selectedOrigen.required' => 'Debe seleccionar el origen de la devolución',
            'productosSeleccionados.required' => 'Debe agregar al menos un producto',
            'productosSeleccionados.min' => 'Debe agregar al menos un producto',
        ]);

        DB::beginTransaction();
        try {
            // Obtener bodega destino
            $bodegaId = (int)str_replace('B', '', $this->selectedDestino['id']);

            // Calcular total
            $total = $this->subtotal;

            // Crear devolución
            $devolucion = Devolucion::create([
                'fecha' => now(),
                'no_formulario' => null,
                'correlativo' => $this->correlativo,
                'no_serie' => $this->no_serie,
                'total' => $total,
                'id_usuario' => Auth::id(),
                'id_tarjeta' => null,
                'id_bodega' => $bodegaId,
            ]);

            // Procesar productos
            foreach ($this->productosSeleccionados as $producto) {
                $this->procesarProductoDevolucion($devolucion, $producto, $bodegaId);
            }

            // Crear transacción
            $tipoTransaccion = TipoTransaccion::firstOrCreate(['nombre' => 'Devolución']);
            Transaccion::create([
                'fecha' => now(),
                'descripcion' => 'Devolución de material' . ($this->motivo ? ' - ' . $this->motivo : ''),
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
     * Procesa un producto de la devolución
     */
    private function procesarProductoDevolucion($devolucion, $producto, $bodegaId)
    {
        // Buscar lote del producto en la bodega destino
        // Aplicar PEPS: el lote más antiguo primero
        $lote = Lote::where('id_producto', $producto['id'])
            ->where('id_bodega', $bodegaId)
            ->where('estado', true)
            ->orderBy('fecha_ingreso', 'asc')
            ->first();

        if ($lote) {
            // Sumar cantidad devuelta al lote existente
            $lote->cantidad += $producto['cantidad'];
            $lote->save();

            $idLote = $lote->id;
        } else {
            // Si no existe lote, crear uno nuevo
            $tipoTransaccion = TipoTransaccion::firstOrCreate(['nombre' => 'Devolución']);
            $transaccion = Transaccion::create([
                'fecha' => now(),
                'descripcion' => 'Lote creado por devolución' . ($this->motivo ? ' - ' . $this->motivo : ''),
                'id_tipo_transaccion' => $tipoTransaccion->id,
            ]);

            $lote = Lote::create([
                'cantidad' => $producto['cantidad'],
                'cantidad_inicial' => $producto['cantidad'],
                'fecha_ingreso' => now(),
                'precio_ingreso' => $producto['precio'],
                'observaciones' => 'Lote creado por devolución' . ($this->motivo ? ': ' . $this->motivo : ''),
                'id_producto' => $producto['id'],
                'id_bodega' => $bodegaId,
                'estado' => true,
                'id_transaccion' => $transaccion->id,
            ]);

            $idLote = $lote->id;
        }

        // Crear detalle de devolución
        DetalleDevolucion::create([
            'id_devolucion' => $devolucion->id,
            'id_producto' => $producto['id'],
            'id_lote' => $idLote,
            'cantidad' => $producto['cantidad'],
        ]);
    }

    /**
     * Abre el modal de confirmación
     *
     * @return void
     */
    public function abrirModalConfirmacion()
    {
        // Validar que haya origen seleccionado
        if (!$this->selectedOrigen) {
            session()->flash('error', 'Debe seleccionar el origen (persona que devuelve).');
            return;
        }

        // Validar que haya destino seleccionado
        if (!$this->selectedDestino) {
            session()->flash('error', 'Debe seleccionar el destino (bodega).');
            return;
        }

        // Validar que haya productos seleccionados
        if (empty($this->productosSeleccionados)) {
            session()->flash('error', 'Debe agregar al menos un producto a la devolución.');
            return;
        }

        $this->showModalConfirmacion = true;
    }

    /**
     * Cierra el modal de confirmación
     *
     * @return void
     */
    public function closeModalConfirmacion()
    {
        $this->showModalConfirmacion = false;
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
