<?php

namespace App\Livewire;

use App\Models\Devolucion;
use App\Models\Salida;
use App\Models\Traslado;
use Livewire\Component;

/**
 * Componente TrasladosHub
 *
 * Dashboard principal del módulo de traslados. Muestra estadísticas del mes
 * (requisiciones, traslados, devoluciones) y un resumen de los movimientos más recientes.
 *
 * @package App\Livewire
 * @see resources/views/livewire/traslados-hub.blade.php
 */
class TrasladosHub extends Component
{
    /** @var array Estadísticas del mes actual */
    public $estadisticas = [];

    /** @var array Listado de traslados recientes */
    public $trasladosRecientes = [];

    /** @var bool Controla modal de detalle */
    public $showModalVer = false;

    /** @var array|null Movimiento seleccionado */
    public $movimientoSeleccionado = null;

    /**
     * Inicializa el componente con datos reales de la BD
     *
     * @return void
     */
    public function mount()
    {
        $this->cargarEstadisticas();
        $this->cargarMovimientosRecientes();
    }

    /**
     * Carga estadísticas del mes actual
     *
     * @return void
     */
    public function cargarEstadisticas()
    {
        $mesActual = now()->month;
        $añoActual = now()->year;

        // Contar requisiciones del mes (Salidas tipo "Uso Interno")
        $requisiciones = Salida::whereMonth('fecha', $mesActual)
            ->whereYear('fecha', $añoActual)
            ->whereHas('tipo', function($q) {
                $q->where('nombre', 'Salida por Uso Interno');
            })
            ->count();

        // Contar traslados del mes
        $traslados = Traslado::whereMonth('fecha', $mesActual)
            ->whereYear('fecha', $añoActual)
            ->count();

        // Contar devoluciones del mes
        $devoluciones = Devolucion::whereMonth('fecha', $mesActual)
            ->whereYear('fecha', $añoActual)
            ->count();

        $this->estadisticas = [
            'requisiciones_mes' => $requisiciones,
            'traslados_mes' => $traslados,
            'devoluciones_mes' => $devoluciones,
            'total_movimientos' => $requisiciones + $traslados + $devoluciones,
        ];
    }

    /**
     * Carga los últimos movimientos (requisiciones, traslados, devoluciones)
     *
     * @return void
     */
    public function cargarMovimientosRecientes()
    {
        $movimientos = [];

        // Obtener requisiciones recientes (Salidas)
        $salidas = Salida::with(['bodega', 'persona', 'tipo', 'detallesSalida.producto'])
            ->whereHas('tipo', function($q) {
                $q->where('nombre', 'Salida por Uso Interno');
            })
            ->orderBy('fecha', 'desc')
            ->limit(3)
            ->get()
            ->map(function($salida) {
                return [
                    'id' => $salida->id,
                    'tipo' => 'Requisición',
                    'tipo_clase' => 'salida',
                    'correlativo' => $salida->ubicacion ?? 'REQ-' . $salida->id,
                    'origen' => $salida->bodega->nombre ?? 'N/A',
                    'destino' => $salida->persona ?
                        trim($salida->persona->nombres . ' ' . $salida->persona->apellidos) : 'N/A',
                    'fecha' => $salida->fecha->format('Y-m-d'),
                    'total' => $salida->total,
                    'productos_count' => $salida->detallesSalida->count(),
                    'estado' => 'Completado',
                    'activo' => true,
                ];
            });

        // Obtener traslados recientes
        $trasladosRecientes = Traslado::with(['bodegaOrigen', 'bodegaDestino', 'detallesTraslado.producto'])
            ->orderBy('fecha', 'desc')
            ->limit(3)
            ->get()
            ->map(function($traslado) {
                return [
                    'id' => $traslado->id,
                    'tipo' => 'Traslado',
                    'tipo_clase' => 'traslado',
                    'correlativo' => $traslado->correlativo ?? 'TRA-' . $traslado->id,
                    'origen' => $traslado->bodegaOrigen->nombre ?? 'N/A',
                    'destino' => $traslado->bodegaDestino->nombre ?? 'N/A',
                    'fecha' => $traslado->fecha->format('Y-m-d'),
                    'total' => $traslado->total,
                    'productos_count' => $traslado->detallesTraslado->count(),
                    'estado' => $traslado->estado,
                    'activo' => $traslado->activo,
                ];
            });

        // Obtener devoluciones recientes
        $devolucionesRecientes = Devolucion::with(['bodega', 'detalles.producto'])
            ->orderBy('fecha', 'desc')
            ->limit(3)
            ->get()
            ->map(function($devolucion) {
                return [
                    'id' => $devolucion->id,
                    'tipo' => 'Devolución',
                    'tipo_clase' => 'devolucion',
                    'correlativo' => $devolucion->no_formulario ?? 'DEV-' . $devolucion->id,
                    'origen' => 'Devolución',
                    'destino' => $devolucion->bodega->nombre ?? 'N/A',
                    'fecha' => $devolucion->fecha->format('Y-m-d'),
                    'total' => $devolucion->total,
                    'productos_count' => $devolucion->detalles->count(),
                    'estado' => 'Completado',
                    'activo' => true,
                ];
            });

        // Combinar todos los movimientos
        $movimientos = $salidas->concat($trasladosRecientes)->concat($devolucionesRecientes);

        // Ordenar por fecha descendente y tomar los últimos 5
        $this->trasladosRecientes = $movimientos
            ->sortByDesc('fecha')
            ->take(5)
            ->values()
            ->toArray();
    }

    /**
     * Muestra el detalle de un movimiento
     *
     * @param int $id
     * @param string $tipo
     * @return void
     */
    public function verDetalle($id, $tipo)
    {
        try {
            switch ($tipo) {
                case 'salida':
                    $salida = Salida::with(['bodega', 'persona', 'detallesSalida.producto', 'detallesSalida.lote'])
                        ->findOrFail($id);

                    $this->movimientoSeleccionado = [
                        'tipo' => 'Requisición',
                        'correlativo' => $salida->ubicacion ?? 'REQ-' . $salida->id,
                        'origen' => $salida->bodega->nombre ?? 'N/A',
                        'destino' => $salida->persona ?
                            trim($salida->persona->nombres . ' ' . $salida->persona->apellidos) : 'N/A',
                        'fecha' => $salida->fecha->format('d/m/Y'),
                        'total' => $salida->total,
                        'observaciones' => $salida->descripcion,
                        'productos' => $salida->detallesSalida->map(function($detalle) {
                            return [
                                'codigo' => $detalle->producto->id,
                                'descripcion' => $detalle->producto->descripcion,
                                'cantidad' => $detalle->cantidad,
                                'precio' => $detalle->precio_salida,
                                'subtotal' => $detalle->cantidad * $detalle->precio_salida,
                            ];
                        })->toArray(),
                    ];
                    break;

                case 'traslado':
                    $traslado = Traslado::with(['bodegaOrigen', 'bodegaDestino', 'detallesTraslado.producto', 'detallesTraslado.lote'])
                        ->findOrFail($id);

                    $this->movimientoSeleccionado = [
                        'tipo' => 'Traslado',
                        'correlativo' => $traslado->correlativo ?? 'TRA-' . $traslado->id,
                        'origen' => $traslado->bodegaOrigen->nombre ?? 'N/A',
                        'destino' => $traslado->bodegaDestino->nombre ?? 'N/A',
                        'fecha' => $traslado->fecha->format('d/m/Y'),
                        'total' => $traslado->total,
                        'observaciones' => $traslado->observaciones,
                        'productos' => $traslado->detallesTraslado->map(function($detalle) {
                            return [
                                'codigo' => $detalle->producto->id,
                                'descripcion' => $detalle->producto->descripcion,
                                'cantidad' => $detalle->cantidad,
                                'precio' => $detalle->lote->precio_ingreso ?? 0,
                                'subtotal' => $detalle->cantidad * ($detalle->lote->precio_ingreso ?? 0),
                            ];
                        })->toArray(),
                    ];
                    break;

                case 'devolucion':
                    $devolucion = Devolucion::with(['bodega', 'detalles.producto', 'detalles.lote'])
                        ->findOrFail($id);

                    $this->movimientoSeleccionado = [
                        'tipo' => 'Devolución',
                        'correlativo' => $devolucion->no_formulario ?? 'DEV-' . $devolucion->id,
                        'origen' => 'Devolución',
                        'destino' => $devolucion->bodega->nombre ?? 'N/A',
                        'fecha' => $devolucion->fecha->format('d/m/Y'),
                        'total' => $devolucion->total,
                        'observaciones' => '',
                        'productos' => $devolucion->detalles->map(function($detalle) {
                            $precio = $detalle->lote ? $detalle->lote->precio_ingreso : 0;
                            return [
                                'codigo' => $detalle->producto->id,
                                'descripcion' => $detalle->producto->descripcion,
                                'cantidad' => $detalle->cantidad,
                                'precio' => $precio,
                                'subtotal' => $detalle->cantidad * $precio,
                            ];
                        })->toArray(),
                    ];
                    break;
            }

            $this->showModalVer = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el detalle: ' . $e->getMessage());
        }
    }

    /**
     * Cierra el modal de detalle
     *
     * @return void
     */
    public function closeModalVer()
    {
        $this->showModalVer = false;
        $this->movimientoSeleccionado = null;
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.traslados-hub');
    }
}
