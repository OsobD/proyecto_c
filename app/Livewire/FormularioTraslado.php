<?php

namespace App\Livewire;

use App\Models\Bitacora;
use App\Models\Bodega;
use App\Models\DetalleTraslado;
use App\Models\Lote;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\TarjetaProducto;
use App\Models\TarjetaResponsabilidad;
use App\Models\Traslado;
use App\Models\TipoTransaccion;
use App\Models\Transaccion;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Componente FormularioTraslado
 *
 * Formulario para registrar traslados de productos entre bodegas físicas.
 * Flujo: Bodega Origen → Bodega Destino
 *
 * @package App\Livewire
 * @see resources/views/livewire/formulario-traslado.blade.php
 */
class FormularioTraslado extends Component
{
    /** @var string Término de búsqueda para bodega origen */
    public $searchOrigen = '';

    /** @var string Término de búsqueda para bodega destino */
    public $searchDestino = '';

    /** @var string Término de búsqueda de persona */
    public $searchPersona = '';

    /** @var string Término de búsqueda de producto */
    public $searchProducto = '';

    /** @var array|null Bodega origen seleccionada */
    public $selectedOrigen = null;

    /** @var array|null Bodega destino seleccionada */
    public $selectedDestino = null;

    /** @var array|null Persona responsable seleccionada */
    public $selectedPersona = null;

    /** @var bool Controla dropdown de bodega origen */
    public $showOrigenDropdown = false;

    /** @var bool Controla dropdown de bodega destino */
    public $showDestinoDropdown = false;

    /** @var bool Controla dropdown de persona */
    public $showPersonaDropdown = false;

    /** @var bool Controla dropdown de productos */
    public $showProductoDropdown = false;

    /** @var array Productos agregados al traslado */
    public $productosSeleccionados = [];

    /** @var string Número correlativo del traslado */
    public $correlativo = '';

    /** @var string Observaciones del traslado */
    public $observaciones = '';

    /** @var bool Controla modal de confirmación */
    public $showModalConfirmacion = false;

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

        // Excluir la bodega destino si ya está seleccionada
        if ($this->selectedDestino) {
            $query->where('id', '!=', $this->selectedDestino['bodega_id']);
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
     * Obtiene bodegas físicas activas filtradas por búsqueda (destino)
     *
     * @return array
     */
    public function getDestinoResultsProperty()
    {
        $query = Bodega::where('activo', true);

        if (!empty($this->searchDestino)) {
            $query->where('nombre', 'like', '%' . $this->searchDestino . '%');
        }

        // Excluir la bodega origen si ya está seleccionada
        if ($this->selectedOrigen) {
            $query->where('id', '!=', $this->selectedOrigen['bodega_id']);
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
     * Selecciona una bodega como destino
     *
     * @param string $id
     * @param string $nombre
     * @param string $tipo
     * @param int $bodegaId
     * @return void
     */
    public function selectDestino($id, $nombre, $tipo, $bodegaId)
    {
        $this->selectedDestino = [
            'id' => $id,
            'nombre' => $nombre,
            'tipo' => $tipo,
            'bodega_id' => $bodegaId
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
     * Obtiene personas activas filtradas por búsqueda
     *
     * @return array
     */
    public function getPersonaResultsProperty()
    {
        $query = Persona::where('estado', true);

        if (!empty($this->searchPersona)) {
            $query->where(function($q) {
                $q->where('nombres', 'like', '%' . $this->searchPersona . '%')
                  ->orWhere('apellidos', 'like', '%' . $this->searchPersona . '%')
                  ->orWhereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ['%' . $this->searchPersona . '%']);
            });
        }

        return $query->get()->map(function ($persona) {
            return [
                'id' => $persona->id,
                'nombre_completo' => $persona->nombres . ' ' . $persona->apellidos,
            ];
        })->toArray();
    }

    /**
     * Selecciona una persona responsable
     *
     * @param int $id
     * @param string $nombreCompleto
     * @return void
     */
    public function selectPersona($id, $nombreCompleto)
    {
        $this->selectedPersona = [
            'id' => $id,
            'nombre_completo' => $nombreCompleto,
        ];
        $this->searchPersona = '';
        $this->showPersonaDropdown = false;
    }

    /**
     * Limpia la selección de persona
     *
     * @return void
     */
    public function clearPersona()
    {
        $this->selectedPersona = null;
        $this->searchPersona = '';
        $this->showPersonaDropdown = false;
    }

    public function updatedSearchOrigen()
    {
        $this->showOrigenDropdown = true;
    }

    public function updatedSearchDestino()
    {
        $this->showDestinoDropdown = true;
    }

    public function updatedSearchPersona()
    {
        $this->showPersonaDropdown = true;
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

    /**
     * Obtiene productos con stock disponible en la bodega origen
     *
     * @return array
     */
    public function getProductoResultsProperty()
    {
        if (!$this->selectedOrigen) {
            return [];
        }

        $bodegaId = $this->selectedOrigen['bodega_id'];
        $search = strtolower(trim($this->searchProducto));

        $query = Producto::where('activo', true)
            ->with(['lotes' => function ($q) use ($bodegaId) {
                $q->where('id_bodega', $bodegaId)
                    ->where('cantidad', '>', 0)
                    ->orderBy('fecha_ingreso', 'asc'); // FIFO
            }]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        return $query->get()
            ->filter(function ($producto) {
                return $producto->lotes->count() > 0; // Solo productos con stock
            })
            ->map(function ($producto) {
                $cantidadDisponible = $producto->lotes->sum('cantidad');
                $precioPromedio = $producto->lotes->avg('precio_ingreso') ?? 0;

                return [
                    'id' => (int)$producto->id,
                    'descripcion' => $producto->descripcion,
                    'es_consumible' => (bool)$producto->es_consumible,
                    'precio' => (float)$precioPromedio,
                    'cantidad_disponible' => (int)$cantidadDisponible,
                    'lotes' => $producto->lotes->toArray()
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Selecciona un producto para agregarlo al traslado
     *
     * @param int $productoId
     * @return void
     */
    public function selectProducto($productoId)
    {
        $producto = collect($this->productoResults)->firstWhere('id', (int)$productoId);

        if ($producto && !collect($this->productosSeleccionados)->contains('id', $producto['id'])) {
            $this->productosSeleccionados[] = [
                'id' => (int)$producto['id'],
                'descripcion' => $producto['descripcion'],
                'es_consumible' => (bool)($producto['es_consumible'] ?? false),
                'precio' => (float)$producto['precio'],
                'cantidad' => 1,
                'cantidad_disponible' => (int)$producto['cantidad_disponible'],
                'lotes' => $producto['lotes']
            ];
        }
        $this->searchProducto = '';
        $this->showProductoDropdown = false;
    }

    /**
     * Elimina un producto de la lista de seleccionados
     *
     * @param int $productoId
     * @return void
     */
    public function eliminarProducto($productoId)
    {
        $this->productosSeleccionados = array_filter($this->productosSeleccionados, function($item) use ($productoId) {
            return $item['id'] !== (int)$productoId;
        });
        $this->productosSeleccionados = array_values($this->productosSeleccionados);
    }

    /**
     * Actualiza la cantidad de un producto seleccionado
     *
     * @param int $productoId
     * @param int $cantidad
     * @return void
     */
    public function actualizarCantidad($productoId, $cantidad)
    {
        foreach ($this->productosSeleccionados as &$producto) {
            if ($producto['id'] === (int)$productoId) {
                $cantidadMax = $producto['cantidad_disponible'];
                $producto['cantidad'] = max(1, min((int)$cantidad, $cantidadMax));
                break;
            }
        }
    }

    /**
     * Calcula el subtotal del traslado
     *
     * @return float
     */
    public function getSubtotalProperty()
    {
        return collect($this->productosSeleccionados)->sum(function($producto) {
            return (int)($producto['cantidad'] ?? 0) * (float)($producto['precio'] ?? 0);
        });
    }

    /**
     * Abre el modal de confirmación con validaciones previas
     *
     * @return void
     */
    public function abrirModalConfirmacion()
    {
        // Validar que haya bodega origen
        if (!$this->selectedOrigen) {
            session()->flash('error', 'Debe seleccionar una bodega de origen.');
            return;
        }

        // Validar que haya bodega destino
        if (!$this->selectedDestino) {
            session()->flash('error', 'Debe seleccionar una bodega de destino.');
            return;
        }

        // Validar que las bodegas sean diferentes
        if ($this->selectedOrigen['bodega_id'] === $this->selectedDestino['bodega_id']) {
            session()->flash('error', 'La bodega de origen y destino no pueden ser la misma.');
            return;
        }

        // Validar que haya productos seleccionados
        if (empty($this->productosSeleccionados)) {
            session()->flash('error', 'Debe agregar al menos un producto al traslado.');
            return;
        }

        // Validar que ningún producto exceda el stock disponible
        foreach ($this->productosSeleccionados as $producto) {
            if ($producto['cantidad'] > $producto['cantidad_disponible']) {
                session()->flash('error', "La cantidad del producto '{$producto['descripcion']}' excede el stock disponible.");
                return;
            }
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
     * Guarda el traslado en la base de datos
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function guardarTraslado()
    {
        try {
            DB::beginTransaction();

            // Obtener el usuario actual
            $usuario = auth()->user();

            // Obtener o crear tarjeta de responsabilidad para la persona seleccionada
            $tarjetaResponsabilidad = null;
            if ($this->selectedPersona) {
                $tarjetaResponsabilidad = TarjetaResponsabilidad::firstOrCreate(
                    [
                        'id_persona' => $this->selectedPersona['id'],
                        'activo' => true,
                    ],
                    [
                        'fecha_creacion' => now(),
                        'total' => 0,
                    ]
                );
            }

            // Crear el traslado
            $traslado = Traslado::create([
                'fecha' => now(),
                'correlativo' => $this->correlativo,
                'no_requisicion' => null,
                'total' => $this->subtotal,
                'descripcion' => null,
                'observaciones' => $this->observaciones,
                'estado' => 'Pendiente',
                'activo' => true,
                'id_bodega_origen' => $this->selectedOrigen['bodega_id'],
                'id_bodega_destino' => $this->selectedDestino['bodega_id'],
                'id_usuario' => $usuario->id,
                'id_tarjeta' => $tarjetaResponsabilidad ? $tarjetaResponsabilidad->id : null,
            ]);

            // Obtener tipos de transacción
            $tipoTraslado = TipoTransaccion::where('nombre', 'Traslado')->first();

            // Procesar cada producto con FIFO
            foreach ($this->productosSeleccionados as $productoData) {
                $cantidadRestante = $productoData['cantidad'];
                $producto = Producto::find($productoData['id']);

                // Obtener lotes ordenados por FIFO
                $lotes = Lote::where('id_producto', $producto->id)
                    ->where('id_bodega', $this->selectedOrigen['bodega_id'])
                    ->where('cantidad', '>', 0)
                    ->orderBy('fecha_ingreso', 'asc')
                    ->get();

                foreach ($lotes as $lote) {
                    if ($cantidadRestante <= 0) break;

                    $cantidadAUsar = min($cantidadRestante, $lote->cantidad);

                    // Crear detalle de traslado
                    DetalleTraslado::create([
                        'id_traslado' => $traslado->id,
                        'id_producto' => $producto->id,
                        'cantidad' => $cantidadAUsar,
                        'id_lote' => $lote->id,
                        'precio_traslado' => $lote->precio_ingreso,
                    ]);

                    // Disminuir cantidad en lote de bodega origen
                    $lote->cantidad -= $cantidadAUsar;
                    $lote->save();

                    // Crear transacción de salida (bodega origen)
                    Transaccion::create([
                        'fecha' => now(),
                        'tipo' => 'Salida',
                        'descripcion' => "Traslado a {$this->selectedDestino['nombre']} - {$producto->descripcion}",
                        'cantidad' => $cantidadAUsar,
                        'id_lote' => $lote->id,
                        'id_tipo_transaccion' => $tipoTraslado->id ?? null,
                        'id_traslado' => $traslado->id,
                    ]);

                    // Crear o actualizar lote en bodega destino
                    $loteDestino = Lote::where('id_producto', $producto->id)
                        ->where('id_bodega', $this->selectedDestino['bodega_id'])
                        ->where('precio_ingreso', $lote->precio_ingreso)
                        ->where('fecha_ingreso', $lote->fecha_ingreso)
                        ->first();

                    if ($loteDestino) {
                        $loteDestino->cantidad += $cantidadAUsar;
                        $loteDestino->save();
                    } else {
                        $loteDestino = Lote::create([
                            'cantidad' => $cantidadAUsar,
                            'precio_ingreso' => $lote->precio_ingreso,
                            'fecha_ingreso' => $lote->fecha_ingreso,
                            'fecha_vencimiento' => $lote->fecha_vencimiento,
                            'id_producto' => $producto->id,
                            'id_bodega' => $this->selectedDestino['bodega_id'],
                        ]);
                    }

                    // Crear transacción de entrada (bodega destino)
                    Transaccion::create([
                        'fecha' => now(),
                        'tipo' => 'Entrada',
                        'descripcion' => "Traslado desde {$this->selectedOrigen['nombre']} - {$producto->descripcion}",
                        'cantidad' => $cantidadAUsar,
                        'id_lote' => $loteDestino->id,
                        'id_tipo_transaccion' => $tipoTraslado->id ?? null,
                        'id_traslado' => $traslado->id,
                    ]);

                    // Si el producto NO es consumible y hay tarjeta de responsabilidad,
                    // agregarlo a la tarjeta (persona es responsable de devolverlo)
                    $esConsumible = $productoData['es_consumible'] ?? false;
                    if (!$esConsumible && $tarjetaResponsabilidad) {
                        TarjetaProducto::create([
                            'precio_asignacion' => $lote->precio_ingreso * $cantidadAUsar,
                            'id_tarjeta' => $tarjetaResponsabilidad->id,
                            'id_producto' => $producto->id,
                            'id_lote' => $loteDestino->id,
                        ]);

                        // Actualizar el total de la tarjeta
                        $tarjetaResponsabilidad->total += ($lote->precio_ingreso * $cantidadAUsar);
                        $tarjetaResponsabilidad->save();
                    }
                    // Si es consumible, solo queda registrado en el traslado asociado a la persona
                    // pero NO se agrega a la tarjeta (persona no es responsable de devolverlo)

                    $cantidadRestante -= $cantidadAUsar;
                }
            }

            // Registrar en bitácora
            Bitacora::create([
                'id_usuario' => $usuario->id,
                'accion' => 'Creación',
                'modelo' => 'App\\Models\\Traslado',
                'modelo_id' => $traslado->id,
                'descripcion' => "Traslado creado: {$this->selectedOrigen['nombre']} → {$this->selectedDestino['nombre']} - Total: Q" . number_format($this->subtotal, 2),
                'created_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            session()->flash('success', 'Traslado registrado exitosamente.');
            return redirect()->route('traslados');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log del error para debugging
            \Log::error('Error al guardar traslado', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'usuario_id' => auth()->id(),
                'bodega_origen' => $this->selectedOrigen['bodega_id'] ?? null,
                'bodega_destino' => $this->selectedDestino['bodega_id'] ?? null,
            ]);

            session()->flash('error', 'Error al registrar el traslado: ' . $e->getMessage());
            $this->showModalConfirmacion = false;
        }
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.formulario-traslado');
    }
}
