<?php

namespace App\Livewire;

use App\Models\Devolucion;
use App\Models\Salida;
use App\Models\Traslado;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente HistorialTraslados
 *
 * Lista completa de traslados (requisiciones, traslados, devoluciones) con filtros
 * avanzados por tipo, estado, fecha y búsqueda. Incluye paginación.
 *
 * @package App\Livewire
 * @see resources/views/livewire/historial-traslados.blade.php
 */
class HistorialTraslados extends Component
{
    use WithPagination;

    /** @var string Término de búsqueda */
    public $search = '';

    /** @var string Fecha inicio para filtro de rango */
    public $fechaInicio = '';

    /** @var string Fecha fin para filtro de rango */
    public $fechaFin = '';

    /** @var string Filtro por tipo (Requisición, Traslado, Devolución) */
    public $tipoFiltro = '';

    /** @var string Filtro por estado */
    public $estadoFiltro = '';

    /** @var bool Controla visibilidad del modal de detalle */
    public $showModalVer = false;

    /** @var array|null Movimiento seleccionado para ver */
    public $movimientoSeleccionado = null;

    /**
     * Se ejecuta cuando cambia el término de búsqueda
     *
     * @return void
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Limpia todos los filtros
     *
     * @return void
     */
    public function limpiarFiltros()
    {
        $this->search = '';
        $this->fechaInicio = '';
        $this->fechaFin = '';
        $this->tipoFiltro = '';
        $this->estadoFiltro = '';
        $this->resetPage();
    }

    /**
     * Obtiene todos los movimientos filtrados desde la BD
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTrasladosFiltradosProperty()
    {
        $movimientos = collect();

        // Cargar Requisiciones (Salidas tipo "Uso Interno")
        if (!$this->tipoFiltro || $this->tipoFiltro === 'Requisición') {
            $salidas = Salida::with(['bodega', 'persona', 'tipo', 'detallesSalida', 'usuario'])
                ->whereHas('tipo', function($q) {
                    $q->where('nombre', 'Salida por Uso Interno');
                })
                ->when($this->search, function($q) {
                    $q->where(function($query) {
                        $query->where('ubicacion', 'like', '%' . $this->search . '%')
                            ->orWhereHas('bodega', function($q) {
                                $q->where('nombre', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('persona', function($q) {
                                $q->whereRaw("CONCAT(nombres, ' ', apellidos) like ?", ['%' . $this->search . '%']);
                            });
                    });
                })
                ->when($this->fechaInicio, function($q) {
                    $q->where('fecha', '>=', $this->fechaInicio);
                })
                ->when($this->fechaFin, function($q) {
                    $q->where('fecha', '<=', $this->fechaFin);
                })
                ->orderBy('fecha', 'desc')
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
                        'usuario' => $salida->usuario ? $salida->usuario->name : 'Sistema',
                        'fecha' => $salida->fecha->format('Y-m-d'),
                        'total' => $salida->total,
                        'productos_count' => $salida->detallesSalida->count(),
                        'estado' => 'Completado',
                        'activo' => true,
                    ];
                });

            $movimientos = $movimientos->concat($salidas);
        }

        // Cargar Traslados
        if (!$this->tipoFiltro || $this->tipoFiltro === 'Traslado') {
            $traslados = Traslado::with(['bodegaOrigen', 'bodegaDestino', 'detallesTraslado', 'usuario'])
                ->when($this->search, function($q) {
                    $q->where(function($query) {
                        $query->where('correlativo', 'like', '%' . $this->search . '%')
                            ->orWhereHas('bodegaOrigen', function($q) {
                                $q->where('nombre', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('bodegaDestino', function($q) {
                                $q->where('nombre', 'like', '%' . $this->search . '%');
                            });
                    });
                })
                ->when($this->estadoFiltro, function($q) {
                    $q->where('estado', $this->estadoFiltro);
                })
                ->when($this->fechaInicio, function($q) {
                    $q->where('fecha', '>=', $this->fechaInicio);
                })
                ->when($this->fechaFin, function($q) {
                    $q->where('fecha', '<=', $this->fechaFin);
                })
                ->orderBy('fecha', 'desc')
                ->get()
                ->map(function($traslado) {
                    return [
                        'id' => $traslado->id,
                        'tipo' => 'Traslado',
                        'tipo_clase' => 'traslado',
                        'correlativo' => $traslado->correlativo ?? 'TRA-' . $traslado->id,
                        'origen' => $traslado->bodegaOrigen->nombre ?? 'N/A',
                        'destino' => $traslado->bodegaDestino->nombre ?? 'N/A',
                        'usuario' => $traslado->usuario ? $traslado->usuario->name : 'Sistema',
                        'fecha' => $traslado->fecha->format('Y-m-d'),
                        'total' => $traslado->total,
                        'productos_count' => $traslado->detallesTraslado->count(),
                        'estado' => $traslado->estado,
                        'activo' => $traslado->activo,
                    ];
                });

            $movimientos = $movimientos->concat($traslados);
        }

        // Cargar Devoluciones
        if (!$this->tipoFiltro || $this->tipoFiltro === 'Devolución') {
            $devoluciones = Devolucion::with(['bodega', 'detalles', 'usuario'])
                ->when($this->search, function($q) {
                    $q->where(function($query) {
                        $query->where('no_formulario', 'like', '%' . $this->search . '%')
                            ->orWhereHas('bodega', function($q) {
                                $q->where('nombre', 'like', '%' . $this->search . '%');
                            });
                    });
                })
                ->when($this->fechaInicio, function($q) {
                    $q->where('fecha', '>=', $this->fechaInicio);
                })
                ->when($this->fechaFin, function($q) {
                    $q->where('fecha', '<=', $this->fechaFin);
                })
                ->orderBy('fecha', 'desc')
                ->get()
                ->map(function($devolucion) {
                    return [
                        'id' => $devolucion->id,
                        'tipo' => 'Devolución',
                        'tipo_clase' => 'devolucion',
                        'correlativo' => $devolucion->no_formulario ?? 'DEV-' . $devolucion->id,
                        'origen' => 'Devolución',
                        'destino' => $devolucion->bodega->nombre ?? 'N/A',
                        'usuario' => $devolucion->usuario ? $devolucion->usuario->name : 'Sistema',
                        'fecha' => $devolucion->fecha->format('Y-m-d'),
                        'total' => $devolucion->total,
                        'productos_count' => $devolucion->detalles->count(),
                        'estado' => 'Completado',
                        'activo' => true,
                    ];
                });

            $movimientos = $movimientos->concat($devoluciones);
        }

        // Aplicar filtro de estado si no es traslado
        if ($this->estadoFiltro && $this->tipoFiltro !== 'Traslado') {
            $movimientos = $movimientos->filter(function($mov) {
                return $mov['estado'] === $this->estadoFiltro;
            });
        }

        // Ordenar por fecha descendente
        return $movimientos->sortByDesc('fecha')->values();
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
        return view('livewire.historial-traslados');
    }
}
