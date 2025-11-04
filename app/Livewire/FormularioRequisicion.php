<?php

namespace App\Livewire;

use App\Models\Bitacora;
use App\Models\Bodega;
use App\Models\DetalleSalida;
use App\Models\Lote;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\Salida;
use App\Models\TarjetaProducto;
use App\Models\TarjetaResponsabilidad;
use App\Models\TipoSalida;
use App\Models\TipoTransaccion;
use App\Models\Transaccion;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Componente FormularioRequisicion
 *
 * Formulario para registrar requisiciones de productos desde bodega hacia
 * tarjetas de responsabilidad (empleados).
 * Flujo: Bodega → Tarjeta de Responsabilidad (Empleado)
 *
 * @package App\Livewire
 * @see resources/views/livewire/formulario-requisicion.blade.php
 */
class FormularioRequisicion extends Component
{
    /** @var string Término de búsqueda para bodega origen */
    public $searchOrigen = '';

    /** @var string Término de búsqueda para empleado destino */
    public $searchDestino = '';

    /** @var string Término de búsqueda de producto */
    public $searchProducto = '';

    /** @var array|null Bodega origen seleccionada */
    public $selectedOrigen = null;

    /** @var array|null Empleado destino seleccionado */
    public $selectedDestino = null;

    /** @var bool Controla dropdown de bodega origen */
    public $showOrigenDropdown = false;

    /** @var bool Controla dropdown de empleado destino */
    public $showDestinoDropdown = false;

    /** @var bool Controla dropdown de productos */
    public $showProductoDropdown = false;

    /** @var array Productos agregados a la requisición */
    public $productosSeleccionados = [];

    /** @var string|null Correlativo de la requisición */
    public $correlativo = null;

    /** @var string|null Observaciones de la requisición */
    public $observaciones = null;

    /**
     * Inicializa el componente
     *
     * @return void
     */
    public function mount()
    {
        $this->productosSeleccionados = [];
    }

    /**
     * Obtiene bodegas físicas activas filtradas por búsqueda
     *
     * @return array
     */
    public function getOrigenResultsProperty()
    {
        $query = Bodega::where('activo', true);

        if (!empty($this->searchOrigen)) {
            $query->where('nombre', 'like', '%' . $this->searchOrigen . '%');
        }

        return $query->get()->map(function ($bodega) {
            return [
                'id' => 'B' . $bodega->id,
                'nombre' => $bodega->nombre,
                'tipo' => 'Bodega',
                'bodega_id' => $bodega->id
            ];
        })->toArray();
    }

    /**
     * Obtiene todas las personas activas con indicador de tarjeta
     *
     * @return array
     */
    public function getDestinoResultsProperty()
    {
        $query = Persona::where('estado', true)
            ->with(['tarjetasResponsabilidad' => function ($q) {
                $q->where('activo', true)->latest();
            }]);

        if (!empty($this->searchDestino)) {
            $query->where(function ($q) {
                $q->where('nombres', 'like', '%' . $this->searchDestino . '%')
                    ->orWhere('apellidos', 'like', '%' . $this->searchDestino . '%')
                    ->orWhereRaw("CONCAT(nombres, ' ', apellidos) like ?", ['%' . $this->searchDestino . '%']);
            });
        }

        return $query->get()->map(function ($persona) {
            $tarjeta = $persona->tarjetasResponsabilidad->first();
            $nombreCompleto = trim($persona->nombres . ' ' . $persona->apellidos);

            return [
                'id' => 'P' . $persona->id,
                'nombre' => $nombreCompleto,
                'tipo' => $tarjeta ? 'Con Tarjeta' : 'Sin Tarjeta',
                'persona_id' => $persona->id,
                'tarjeta_id' => $tarjeta ? $tarjeta->id : null,
                'tiene_tarjeta' => $tarjeta !== null
            ];
        })->toArray();
    }

    /**
     * Obtiene productos disponibles en la bodega seleccionada
     *
     * @return array
     */
    public function getProductoResultsProperty()
    {
        // Si no hay bodega seleccionada, no mostrar productos
        if (!$this->selectedOrigen) {
            return [];
        }

        $bodegaId = $this->selectedOrigen['bodega_id'];

        // Obtener productos con lotes disponibles en la bodega
        $query = Producto::where('activo', true)
            ->whereHas('lotes', function ($q) use ($bodegaId) {
                $q->where('id_bodega', $bodegaId)
                    ->where('cantidad', '>', 0)
                    ->where('estado', true);
            })
            ->with(['lotes' => function ($q) use ($bodegaId) {
                $q->where('id_bodega', $bodegaId)
                    ->where('cantidad', '>', 0)
                    ->where('estado', true)
                    ->orderBy('fecha_ingreso', 'asc'); // FIFO
            }, 'categoria']);

        // Filtrar por búsqueda si existe
        if (!empty($this->searchProducto)) {
            $search = trim($this->searchProducto);
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        return $query->get()->map(function ($producto) {
            $cantidadTotal = $producto->lotes->sum('cantidad');
            $precioPromedio = $producto->lotes->avg('precio_ingreso') ?? 0;

            return [
                'id' => $producto->id,
                'descripcion' => $producto->descripcion,
                'categoria' => $producto->categoria->nombre ?? '',
                'cantidad_disponible' => $cantidadTotal,
                'precio' => $precioPromedio,
                'lotes' => $producto->lotes->toArray()
            ];
        })->toArray();
    }

    /**
     * Selecciona una bodega como origen
     *
     * @param string $id
     * @param string $nombre
     * @param string $tipo
     * @param int $bodegaId
     * @return void
     */
    public function selectOrigen($id, $nombre, $tipo, $bodegaId)
    {
        $this->selectedOrigen = [
            'id' => $id,
            'nombre' => $nombre,
            'tipo' => $tipo,
            'bodega_id' => $bodegaId
        ];
        $this->searchOrigen = '';
        $this->showOrigenDropdown = false;

        // Limpiar productos seleccionados al cambiar de bodega
        $this->productosSeleccionados = [];
    }

    /**
     * Selecciona una persona como destino
     *
     * @param string $id
     * @param string $nombre
     * @param string $tipo
     * @param int $personaId
     * @param int|null $tarjetaId
     * @param bool $tieneTarjeta
     * @return void
     */
    public function selectDestino($id, $nombre, $tipo, $personaId, $tarjetaId, $tieneTarjeta)
    {
        $this->selectedDestino = [
            'id' => $id,
            'nombre' => $nombre,
            'tipo' => $tipo,
            'persona_id' => $personaId,
            'tarjeta_id' => $tarjetaId,
            'tiene_tarjeta' => $tieneTarjeta
        ];
        $this->searchDestino = '';
        $this->showDestinoDropdown = false;
    }

    /**
     * Limpia la selección de origen
     *
     * @return void
     */
    public function clearOrigen()
    {
        $this->selectedOrigen = null;
        $this->searchOrigen = '';
        $this->showOrigenDropdown = false;
        $this->productosSeleccionados = [];
    }

    /**
     * Limpia la selección de destino
     *
     * @return void
     */
    public function clearDestino()
    {
        $this->selectedDestino = null;
        $this->searchDestino = '';
        $this->showDestinoDropdown = false;
    }

    /**
     * Se ejecuta cuando cambia el término de búsqueda de origen
     *
     * @return void
     */
    public function updatedSearchOrigen()
    {
        $this->showOrigenDropdown = true;
    }

    /**
     * Se ejecuta cuando cambia el término de búsqueda de destino
     *
     * @return void
     */
    public function updatedSearchDestino()
    {
        $this->showDestinoDropdown = true;
    }

    /**
     * Se ejecuta cuando cambia el término de búsqueda de producto
     *
     * @return void
     */
    public function updatedSearchProducto()
    {
        $this->showProductoDropdown = true;
    }

    /**
     * Selecciona el primer resultado de producto (útil para Enter)
     *
     * @return void
     */
    public function seleccionarPrimerResultado()
    {
        $resultados = $this->productoResults;
        if (!empty($resultados)) {
            $primerProducto = array_values($resultados)[0];
            $this->selectProducto($primerProducto['id']);
        }
    }

    /**
     * Agrega un producto a la requisición
     *
     * @param string $productoId
     * @return void
     */
    public function selectProducto($productoId)
    {
        $productos = $this->productoResults;
        $producto = collect($productos)->firstWhere('id', $productoId);

        if ($producto && !collect($this->productosSeleccionados)->contains('id', $producto['id'])) {
            $this->productosSeleccionados[] = [
                'id' => $producto['id'],
                'descripcion' => $producto['descripcion'],
                'precio' => (float) $producto['precio'],
                'cantidad' => 1,
                'cantidad_disponible' => (int) $producto['cantidad_disponible'],
                'lotes' => $producto['lotes']
            ];
        }

        $this->searchProducto = '';
        $this->showProductoDropdown = false;
    }

    /**
     * Elimina un producto de la requisición
     *
     * @param string $productoId
     * @return void
     */
    public function eliminarProducto($productoId)
    {
        $this->productosSeleccionados = array_values(array_filter($this->productosSeleccionados, function ($item) use ($productoId) {
            return $item['id'] !== $productoId;
        }));
    }

    /**
     * Actualiza la cantidad de un producto
     *
     * @param string $productoId
     * @param int $cantidad
     * @return void
     */
    public function actualizarCantidad($productoId, $cantidad)
    {
        foreach ($this->productosSeleccionados as &$producto) {
            if ($producto['id'] === $productoId) {
                // Validar que no exceda el stock disponible
                $cantidadMaxima = $producto['cantidad_disponible'];
                $producto['cantidad'] = max(1, min((int) $cantidad, $cantidadMaxima));
                break;
            }
        }
    }

    /**
     * Calcula el subtotal de la requisición
     *
     * @return float
     */
    public function getSubtotalProperty()
    {
        return collect($this->productosSeleccionados)->sum(function ($producto) {
            return $producto['cantidad'] * $producto['precio'];
        });
    }

    /**
     * Guarda la requisición
     *
     * @return void
     */
    public function save()
    {
        // Validaciones
        $this->validate([
            'selectedOrigen' => 'required',
            'selectedDestino' => 'required',
            'productosSeleccionados' => 'required|array|min:1',
            'correlativo' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ], [
            'selectedOrigen.required' => 'Debe seleccionar una bodega origen.',
            'selectedDestino.required' => 'Debe seleccionar un empleado destino.',
            'productosSeleccionados.required' => 'Debe agregar al menos un producto.',
            'productosSeleccionados.min' => 'Debe agregar al menos un producto.',
        ]);

        // Validar que el destino tenga tarjeta activa
        if (!$this->selectedDestino['tiene_tarjeta']) {
            session()->flash('error', 'El empleado seleccionado no tiene una tarjeta de responsabilidad activa.');
            return;
        }

        try {
            DB::beginTransaction();

            // Obtener tipo de salida "Salida por Uso Interno"
            $tipoSalida = TipoSalida::where('nombre', 'Salida por Uso Interno')->first();
            if (!$tipoSalida) {
                throw new \Exception('No se encontró el tipo de salida "Salida por Uso Interno".');
            }

            // Obtener tipo de transacción "Salida"
            $tipoTransaccion = TipoTransaccion::where('nombre', 'Salida')->first();
            if (!$tipoTransaccion) {
                throw new \Exception('No se encontró el tipo de transacción "Salida".');
            }

            // Obtener ID de usuario (si no está autenticado, usar NULL o un valor predeterminado)
            $userId = auth()->check() ? auth()->id() : 1; // ID 1 como usuario por defecto si no hay autenticación

            // Crear el registro de salida
            $salida = Salida::create([
                'fecha' => now(),
                'total' => $this->subtotal,
                'descripcion' => $this->observaciones ?? 'Requisición de productos',
                'ubicacion' => $this->correlativo,
                'id_usuario' => $userId,
                'id_tarjeta' => null, // Se usará en detalle con TarjetaProducto
                'id_bodega' => $this->selectedOrigen['bodega_id'],
                'id_tipo' => $tipoSalida->id,
                'id_persona' => $this->selectedDestino['persona_id'],
            ]);

            // Crear transacción
            $transaccion = Transaccion::create([
                'id_tipo' => $tipoTransaccion->id,
                'id_salida' => $salida->id,
            ]);

            // Procesar cada producto
            foreach ($this->productosSeleccionados as $producto) {
                $cantidadRestante = $producto['cantidad'];
                $lotes = collect($producto['lotes'])->sortBy('fecha_ingreso'); // FIFO

                foreach ($lotes as $lote) {
                    if ($cantidadRestante <= 0) {
                        break;
                    }

                    $loteModel = Lote::find($lote['id']);
                    if (!$loteModel || $loteModel->cantidad <= 0) {
                        continue;
                    }

                    // Calcular cantidad a tomar de este lote
                    $cantidadDelLote = min($cantidadRestante, $loteModel->cantidad);

                    // Crear detalle de salida
                    DetalleSalida::create([
                        'id_salida' => $salida->id,
                        'id_producto' => $producto['id'],
                        'id_lote' => $loteModel->id,
                        'cantidad' => $cantidadDelLote,
                        'precio_salida' => $loteModel->precio_ingreso,
                    ]);

                    // Actualizar cantidad del lote
                    $loteModel->cantidad -= $cantidadDelLote;
                    $loteModel->save();

                    // Crear asignación en tarjeta de responsabilidad
                    TarjetaProducto::create([
                        'precio_asignacion' => $loteModel->precio_ingreso,
                        'id_tarjeta' => $this->selectedDestino['tarjeta_id'],
                        'id_producto' => $producto['id'],
                        'id_lote' => $loteModel->id,
                    ]);

                    $cantidadRestante -= $cantidadDelLote;
                }

                // Validar que se pudo procesar toda la cantidad
                if ($cantidadRestante > 0) {
                    throw new \Exception("No hay suficiente stock disponible para el producto: {$producto['descripcion']}");
                }
            }

            // Actualizar total de la tarjeta de responsabilidad
            $tarjeta = TarjetaResponsabilidad::find($this->selectedDestino['tarjeta_id']);
            if ($tarjeta) {
                $tarjeta->total += $this->subtotal;
                $tarjeta->save();
            }

            // Registrar en bitácora
            $userName = auth()->check() && auth()->user() ? auth()->user()->name : 'Sistema';
            Bitacora::create([
                'accion' => 'crear',
                'modelo' => 'Salida',
                'modelo_id' => $salida->id,
                'descripcion' => $userName . " creó Requisición #{$salida->id} desde bodega '{$this->selectedOrigen['nombre']}' hacia '{$this->selectedDestino['nombre']}'",
                'datos_anteriores' => null,
                'datos_nuevos' => json_encode([
                    'id_salida' => $salida->id,
                    'bodega' => $this->selectedOrigen['nombre'],
                    'persona' => $this->selectedDestino['nombre'],
                    'total' => $this->subtotal,
                    'correlativo' => $this->correlativo,
                ]),
                'id_usuario' => $userId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();

            session()->flash('success', 'Requisición registrada exitosamente.');

            // Limpiar formulario
            $this->reset([
                'selectedOrigen',
                'selectedDestino',
                'productosSeleccionados',
                'correlativo',
                'observaciones',
                'searchOrigen',
                'searchDestino',
                'searchProducto'
            ]);

            // Redirigir a la lista de requisiciones o salidas
            return redirect()->route('salidas');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log del error para debugging
            \Log::error('Error al registrar requisición: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error al registrar la requisición: ' . $e->getMessage());
        }
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.formulario-requisicion');
    }
}
