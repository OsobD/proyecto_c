<?php

namespace App\Livewire;

use App\Models\Bitacora;
use App\Models\Bodega;
use App\Models\ConsumiblePersona;
use App\Models\DetalleSalida;
use App\Models\DetalleTraslado;
use App\Models\Lote;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\Salida;
use App\Models\TarjetaProducto;
use App\Models\TarjetaResponsabilidad;
use App\Models\TipoSalida;
use App\Models\TipoTransaccion;
use App\Models\Transaccion;
use App\Models\Traslado;
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

    /** @var string|null Número de serie de la requisición */
    public $numeroSerie = null;

    /** @var string|null Observaciones de la requisición */
    public $observaciones = null;

    /** @var bool Controla modal de confirmación */
    public $showModalConfirmacion = false;

    /**
     * Listeners de eventos
     */
    protected $listeners = ['personaCreada' => 'handlePersonaCreada'];

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
     * Maneja el evento cuando se crea una persona desde el modal
     *
     * @param array $personaData
     * @param string $mensaje
     * @return void
     */
    public function handlePersonaCreada($personaData, $mensaje)
    {
        // Obtener la persona recién creada para verificar si tiene tarjeta
        $persona = Persona::with(['tarjetasResponsabilidad' => function ($q) {
            $q->where('activo', true)->latest();
        }])->find($personaData['id']);

        if ($persona) {
            $tarjeta = $persona->tarjetasResponsabilidad->first();

            // Seleccionar automáticamente la persona recién creada
            $this->selectedDestino = [
                'id' => 'P' . $persona->id,
                'nombre' => $personaData['nombre_completo'],
                'tipo' => $tarjeta ? 'Con Tarjeta' : 'Sin Tarjeta',
                'persona_id' => $persona->id,
                'tarjeta_id' => $tarjeta ? $tarjeta->id : null,
                'tiene_tarjeta' => $tarjeta !== null
            ];

            $this->showDestinoDropdown = false;
            session()->flash('success', $mensaje);
        }
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
            // Si hay búsqueda, filtrar por nombre
            $query->where(function ($q) {
                $q->where('nombres', 'like', '%' . $this->searchDestino . '%')
                    ->orWhere('apellidos', 'like', '%' . $this->searchDestino . '%')
                    ->orWhereRaw("CONCAT(nombres, ' ', apellidos) like ?", ['%' . $this->searchDestino . '%']);
            });
        } else {
            // Si no hay búsqueda, limitar a 5 resultados iniciales
            $query->limit(5);
        }

        return $query->get()->map(function ($persona) {
            $tarjeta = $persona->tarjetasResponsabilidad->first();
            $nombreCompleto = trim($persona->nombres . ' ' . $persona->apellidos);

            return [
                'id' => 'P' . $persona->id,
                'nombre' => $nombreCompleto,
                'tipo' => 'DPI: ' . ($persona->dpi ?? 'N/A'),
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
                $q->whereHas('ubicaciones', function ($q2) use ($bodegaId) {
                    $q2->where('id_bodega', $bodegaId)
                       ->where('cantidad', '>', 0);
                })->where('estado', true);
            })
            ->with(['lotes' => function ($q) use ($bodegaId) {
                $q->whereHas('ubicaciones', function ($q2) use ($bodegaId) {
                    $q2->where('id_bodega', $bodegaId)
                       ->where('cantidad', '>', 0);
                })
                ->with(['ubicaciones' => function ($q3) use ($bodegaId) {
                    $q3->where('id_bodega', $bodegaId);
                }])
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
            $cantidadTotal = $producto->lotes->sum(function ($lote) {
                return $lote->ubicaciones->first()?->cantidad ?? 0;
            });
            $precioPromedio = $producto->lotes->avg('precio_ingreso') ?? 0;

            return [
                'id' => $producto->id,
                'descripcion' => $producto->descripcion,
                'es_consumible' => (bool)$producto->es_consumible,
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
                'es_consumible' => (bool)($producto['es_consumible'] ?? false),
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
            $cantidad = (float)($producto['cantidad'] ?? 0);
            $precio = (float)($producto['precio'] ?? 0);
            return $cantidad * $precio;
        });
    }

    /**
     * Abre el modal de confirmación después de validar
     *
     * @return void
     */
    public function abrirModalConfirmacion()
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

        // Validar correlativo único si se proporcionó
        if (!empty($this->correlativo)) {
            $correlativoExiste = Salida::where('ubicacion', $this->correlativo)->exists() ||
                                 Traslado::where('correlativo', $this->correlativo)->exists() ||
                                 Traslado::where('no_requisicion', $this->correlativo)->exists();

            if ($correlativoExiste) {
                $this->addError('correlativo', "El correlativo '{$this->correlativo}' ya está en uso. Por favor, utilice uno diferente.");
                return;
            }
        }

        // Nota: No validamos si tiene tarjeta porque se crea automáticamente si no existe

        // Validar que ningún producto exceda el stock disponible
        foreach ($this->productosSeleccionados as $producto) {
            if ($producto['cantidad'] > $producto['cantidad_disponible']) {
                session()->flash('error', "La cantidad del producto '{$producto['descripcion']}' excede el stock disponible.");
                return;
            }
        }

        // Abrir modal de confirmación
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
     * Guarda la requisición
     *
     * @return void
     */
    public function guardarRequisicion()
    {
        try {
            DB::beginTransaction();

            // Validar que el correlativo no esté duplicado en salidas o traslados
            $correlativoExiste = Salida::where('ubicacion', $this->correlativo)->exists() ||
                                 Traslado::where('correlativo', $this->correlativo)->exists() ||
                                 Traslado::where('no_requisicion', $this->correlativo)->exists();

            if ($correlativoExiste) {
                throw new \Exception("El correlativo '{$this->correlativo}' ya está en uso. Por favor, utilice uno diferente.");
            }

            // Obtener ID de usuario
            $userId = auth()->check() ? auth()->id() : 1;

            // Obtener o crear tarjeta de responsabilidad para la persona
            $tarjeta = TarjetaResponsabilidad::firstOrCreate(
                [
                    'id_persona' => $this->selectedDestino['persona_id'],
                    'activo' => true,
                ],
                [
                    'fecha_creacion' => now(),
                    'total' => 0,
                ]
            );

            // Separar productos por tipo
            $productosConsumibles = collect($this->productosSeleccionados)->filter(fn($p) => $p['es_consumible'] ?? false);
            $productosNoConsumibles = collect($this->productosSeleccionados)->filter(fn($p) => !($p['es_consumible'] ?? false));

            $registrosCreados = [];

            // ============ PROCESAR PRODUCTOS CONSUMIBLES (TRASLADO) ============
            if ($productosConsumibles->isNotEmpty()) {
                // Obtener tipo de transacción "Traslado"
                $tipoTraslado = TipoTransaccion::where('nombre', 'Traslado')->first();

                // Crear traslado para productos consumibles
                $traslado = Traslado::create([
                    'fecha' => now(),
                    'correlativo' => $this->correlativo,
                    'no_requisicion' => $this->correlativo,
                    'total' => $productosConsumibles->sum(fn($p) => $p['cantidad'] * $p['precio']),
                    'descripcion' => $this->observaciones ?: 'Requisición de productos consumibles',
                    'observaciones' => $this->observaciones,
                    'estado' => 'Completado',
                    'activo' => true,
                    'id_bodega_origen' => $this->selectedOrigen['bodega_id'],
                    'id_bodega_destino' => $this->selectedOrigen['bodega_id'], // Misma bodega (salida lógica)
                    'id_usuario' => $userId,
                    'id_persona' => $this->selectedDestino['persona_id'], // Referencia directa a la persona
                    'id_tarjeta' => null, // No se usa tarjeta para consumibles, se usa consumible_persona
                ]);

                $registrosCreados[] = ['tipo' => 'Traslado', 'id' => $traslado->id];

                // Procesar cada producto consumible
                foreach ($productosConsumibles as $producto) {
                    $this->procesarProductoConsumible(
                        $producto,
                        $traslado,
                        $tipoTraslado,
                        $this->correlativo,
                        $this->selectedDestino['persona_id'],
                        $this->selectedOrigen['bodega_id'],
                        $this->observaciones
                    );
                }
            }

            // ============ PROCESAR PRODUCTOS NO CONSUMIBLES (SALIDA) ============
            if ($productosNoConsumibles->isNotEmpty()) {
                // Obtener tipo de salida y transacción
                $tipoSalida = TipoSalida::where('nombre', 'Salida por Uso Interno')->first();
                $tipoTransaccionSalida = TipoTransaccion::where('nombre', 'Salida')->first();

                if (!$tipoSalida) {
                    throw new \Exception('No se encontró el tipo de salida "Salida por Uso Interno".');
                }

                // Crear salida para productos no consumibles
                $salida = Salida::create([
                    'fecha' => now(),
                    'total' => $productosNoConsumibles->sum(fn($p) => $p['cantidad'] * $p['precio']),
                    'descripcion' => $this->observaciones ?: 'Requisición de productos no consumibles',
                    'ubicacion' => $this->correlativo,
                    'id_usuario' => $userId,
                    'id_tarjeta' => null,
                    'id_bodega' => $this->selectedOrigen['bodega_id'],
                    'id_tipo' => $tipoSalida->id,
                    'id_persona' => $this->selectedDestino['persona_id'],
                ]);

                $registrosCreados[] = ['tipo' => 'Salida', 'id' => $salida->id];

                // Crear transacción
                if ($tipoTransaccionSalida) {
                    Transaccion::create([
                        'id_tipo' => $tipoTransaccionSalida->id,
                        'id_salida' => $salida->id,
                    ]);
                }

                // Procesar cada producto no consumible
                $totalTarjeta = 0;
                foreach ($productosNoConsumibles as $producto) {
                    $totalTarjeta += $this->procesarProductoNoConsumible(
                        $producto,
                        $salida,
                        $tarjeta,
                        $this->selectedOrigen['bodega_id']
                    );
                }

                // Actualizar total de la tarjeta sin triggear eventos
                $tarjeta->total += $totalTarjeta;
                $tarjeta->saveQuietly();
            }

            // Registrar en bitácora
            $userName = auth()->check() && auth()->user() ? auth()->user()->name : 'Sistema';
            $detalleRegistros = collect($registrosCreados)->map(fn($r) => "{$r['tipo']} #{$r['id']}")->join(', ');

            Bitacora::create([
                'accion' => 'crear',
                'modelo' => 'Requisicion',
                'modelo_id' => null,
                'descripcion' => $userName . " creó Requisición desde bodega '{$this->selectedOrigen['nombre']}' hacia '{$this->selectedDestino['nombre']}'. Registros: {$detalleRegistros}",
                'datos_anteriores' => null,
                'datos_nuevos' => json_encode([
                    'registros' => $registrosCreados,
                    'bodega' => $this->selectedOrigen['nombre'],
                    'persona' => $this->selectedDestino['nombre'],
                    'total' => $this->subtotal,
                    'correlativo' => $this->correlativo,
                    'consumibles' => $productosConsumibles->count(),
                    'no_consumibles' => $productosNoConsumibles->count(),
                ]),
                'id_usuario' => $userId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            DB::commit();

            // Cerrar modal
            $this->showModalConfirmacion = false;

            session()->flash('success', 'Requisición registrada exitosamente. ' .
                ($productosConsumibles->isNotEmpty() ? $productosConsumibles->count() . ' consumibles en Traslado. ' : '') .
                ($productosNoConsumibles->isNotEmpty() ? $productosNoConsumibles->count() . ' no consumibles en Salida.' : ''));

            // Limpiar formulario
            $this->reset([
                'selectedOrigen',
                'selectedDestino',
                'productosSeleccionados',
                'correlativo',
                'numeroSerie',
                'observaciones',
                'searchOrigen',
                'searchDestino',
                'searchProducto'
            ]);

            return redirect()->route('traslados');
        } catch (\Exception $e) {
            DB::rollBack();

            // Cerrar modal y mostrar error
            $this->showModalConfirmacion = false;

            \Log::error('Error al registrar requisición: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error al registrar la requisición: ' . $e->getMessage());
        }
    }

    /**
     * Procesa un producto consumible (va a Traslado + ConsumiblePersona)
     */
    private function procesarProductoConsumible($producto, $traslado, $tipoTraslado, $correlativo, $idPersona, $idBodega, $observaciones)
    {
        $cantidadRestante = $producto['cantidad'];
        $lotes = collect($producto['lotes'])->sortBy('fecha_ingreso'); // FIFO

        foreach ($lotes as $lote) {
            if ($cantidadRestante <= 0) break;

            $loteModel = Lote::find($lote['id']);
            $cantidadEnBodega = $loteModel ? $loteModel->cantidadEnBodega($idBodega) : 0;
            
            if (!$loteModel || $cantidadEnBodega <= 0) continue;

            $cantidadDelLote = min($cantidadRestante, $cantidadEnBodega);

            // Crear detalle de traslado
            DetalleTraslado::create([
                'id_traslado' => $traslado->id,
                'id_producto' => $producto['id'],
                'cantidad' => $cantidadDelLote,
                'id_lote' => $loteModel->id,
                'precio_traslado' => $loteModel->precio_ingreso,
            ]);

            // NUEVO: Crear registro histórico en consumible_persona
            ConsumiblePersona::create([
                'correlativo' => $correlativo,
                'fecha' => now(),
                'id_persona' => $idPersona,
                'id_producto' => $producto['id'],
                'id_lote' => $loteModel->id,
                'cantidad' => $cantidadDelLote,
                'precio_unitario' => $loteModel->precio_ingreso,
                'observaciones' => $observaciones,
                'id_bodega' => $idBodega,
            ]);

            // Actualizar cantidad del lote (salida consumible)
            // El flag true indica que es consumo, reduce cantidad_disponible total
            $loteModel->decrementarEnBodega($idBodega, $cantidadDelLote, true);

            // Crear transacción de salida
            if ($tipoTraslado) {
                Transaccion::create([
                    'fecha' => now(),
                    'tipo' => 'Salida',
                    'descripcion' => "Requisición consumible - {$producto['descripcion']}",
                    'cantidad' => $cantidadDelLote,
                    'id_lote' => $loteModel->id,
                    'id_tipo_transaccion' => $tipoTraslado->id,
                    'id_traslado' => $traslado->id,
                ]);
            }

            $cantidadRestante -= $cantidadDelLote;
        }

        if ($cantidadRestante > 0) {
            throw new \Exception("No hay suficiente stock disponible para el producto: {$producto['descripcion']}");
        }
    }

    /**
     * Procesa un producto no consumible (va a Salida + TarjetaProducto)
     */
    private function procesarProductoNoConsumible($producto, $salida, $tarjeta, $idBodega)
    {
        $cantidadRestante = $producto['cantidad'];
        $lotes = collect($producto['lotes'])->sortBy('fecha_ingreso'); // FIFO
        $totalPrecio = 0;

        foreach ($lotes as $lote) {
            if ($cantidadRestante <= 0) break;

            $loteModel = Lote::find($lote['id']);
            $cantidadEnBodega = $loteModel ? $loteModel->cantidadEnBodega($idBodega) : 0;

            if (!$loteModel || $cantidadEnBodega <= 0) continue;

            $cantidadDelLote = min($cantidadRestante, $cantidadEnBodega);

            // Crear detalle de salida
            DetalleSalida::create([
                'id_salida' => $salida->id,
                'id_producto' => $producto['id'],
                'id_lote' => $loteModel->id,
                'cantidad' => $cantidadDelLote,
                'precio_salida' => $loteModel->precio_ingreso,
            ]);

            // Actualizar cantidad del lote (asignación a persona)
            // El flag true indica que reduce cantidad_disponible (producto asignado, no disponible)
            $loteModel->decrementarEnBodega($idBodega, $cantidadDelLote, true);

            // Crear asignación en tarjeta de responsabilidad
            $precioAsignacion = $loteModel->precio_ingreso * $cantidadDelLote;
            TarjetaProducto::create([
                'precio_asignacion' => $precioAsignacion,
                'id_tarjeta' => $tarjeta->id,
                'id_producto' => $producto['id'],
                'id_lote' => $loteModel->id,
            ]);

            $totalPrecio += $precioAsignacion;
            $cantidadRestante -= $cantidadDelLote;
        }

        if ($cantidadRestante > 0) {
            throw new \Exception("No hay suficiente stock disponible para el producto: {$producto['descripcion']}");
        }

        return $totalPrecio;
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
