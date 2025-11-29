<?php

namespace App\Livewire;

use App\Models\SolicitudAprobacion;
use Livewire\Component;

/**
 * Componente AprobacionesPendientes
 *
 * Muestra las requisiciones, traslados y compras que están pendientes de aprobación
 * por parte de usuarios con permisos administrativos.
 *
 * @package App\Livewire
 * @see resources/views/livewire/aprobaciones-pendientes.blade.php
 */
class AprobacionesPendientes extends Component
{
    /** @var array Listado de elementos pendientes de aprobación */
    public $pendientes = [];

    /** @var string Filtro por tabla */
    public $filtroTabla = '';

    /**
     * Inicializa el componente con datos de aprobaciones pendientes
     *
     * @return void
     */
    public function mount()
    {
        $this->cargarPendientes();
    }

    /**
     * Carga las aprobaciones pendientes desde la base de datos
     *
     * @return void
     */
    public function cargarPendientes()
    {
        // Cargar solicitudes pendientes de la base de datos
        $solicitudes = SolicitudAprobacion::with(['solicitante', 'aprobador'])
            ->where('estado', 'PENDIENTE')
            ->when($this->filtroTabla, function ($query) {
                $query->where('tabla', $this->filtroTabla);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $this->pendientes = $solicitudes->map(function ($solicitud) {
            // Determinar el tipo de acción y descripción
            $tipoDescripcion = $this->obtenerDescripcionSolicitud($solicitud);

            return [
                'id' => $solicitud->id,
                'tipo' => strtolower($solicitud->tipo),
                'tabla' => $solicitud->tabla,
                'numero' => $tipoDescripcion['numero'],
                'solicitante' => $solicitud->solicitante ? $solicitud->solicitante->name : 'Desconocido',
                'fecha' => $solicitud->created_at->format('Y-m-d'),
                'fecha_completa' => $solicitud->created_at->format('d/m/Y H:i'),
                'descripcion' => $tipoDescripcion['descripcion'],
                'observaciones' => $solicitud->observaciones,
                'estado' => $solicitud->estado,
                'accion' => $solicitud->tipo,
            ];
        })->toArray();
    }

    /**
     * Obtiene la descripción de la solicitud según la tabla y tipo
     *
     * @param SolicitudAprobacion $solicitud
     * @return array
     */
    private function obtenerDescripcionSolicitud($solicitud)
    {
        $numero = '';
        $descripcion = '';

        $tablaLabel = match ($solicitud->tabla) {
            'salida' => 'Requisición (Salida)',
            'traslado' => 'Traslado/Requisición',
            'compra' => 'Compra',
            'devolucion' => 'Devolución',
            'categoria' => 'Categoría',
            'proveedor' => 'Proveedor',
            'persona' => 'Persona',
            'bodega' => 'Bodega',
            'tarjeta_responsabilidad' => 'Tarjeta de Responsabilidad',
            'producto' => 'Producto',
            default => ucfirst($solicitud->tabla)
        };

        $numero = strtoupper(substr($solicitud->tabla, 0, 3)) . '-' . $solicitud->registro_id;
        $descripcion = "Solicitud de {$solicitud->tipo} de {$tablaLabel}";

        return [
            'numero' => $numero,
            'descripcion' => $descripcion,
        ];
    }

    /**
     * Filtra las aprobaciones por tabla
     *
     * @return array
     */
    public function getPendientesFiltradosProperty()
    {
        if (empty($this->filtroTabla)) {
            return $this->pendientes;
        }

        return array_filter($this->pendientes, function ($item) {
            return $item['tabla'] === $this->filtroTabla;
        });
    }

    /**
     * Aprueba un elemento pendiente
     *
     * @param int $id
     * @return void
     */
    public function aprobar($id)
    {
        try {
            $solicitud = SolicitudAprobacion::findOrFail($id);
            $usuario = auth()->user();

            if (!$usuario) {
                session()->flash('error', 'Debe iniciar sesión.');
                return;
            }

            // Ejecutar la acción según el tipo
            $this->ejecutarAccion($solicitud);

            // Actualizar estado de la solicitud
            $solicitud->update([
                'estado' => 'APROBADA',
                'aprobador_id' => $usuario->id
            ]);

            // Registrar en bitácora
            \App\Models\Bitacora::create([
                'accion' => 'Aprobar Solicitud',
                'modelo' => 'SolicitudAprobacion',
                'modelo_id' => $solicitud->id,
                'descripcion' => "Aprobó solicitud de {$solicitud->tipo} en {$solicitud->tabla} ID {$solicitud->registro_id}",
                'id_usuario' => $usuario->id,
                'created_at' => now(),
            ]);

            session()->flash('success', 'Solicitud aprobada y aplicada exitosamente.');
            $this->cargarPendientes();

        } catch (\Exception $e) {
            \Log::error('Error al aprobar solicitud', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Error al aprobar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Rechaza un elemento pendiente
     *
     * @param int $id
     * @return void
     */
    public function rechazar($id)
    {
        try {
            $solicitud = SolicitudAprobacion::findOrFail($id);
            $usuario = auth()->user();

            if (!$usuario) {
                session()->flash('error', 'Debe iniciar sesión.');
                return;
            }

            // Actualizar estado de la solicitud
            $solicitud->update([
                'estado' => 'RECHAZADA',
                'aprobador_id' => $usuario->id
            ]);

            // Registrar en bitácora
            \App\Models\Bitacora::create([
                'accion' => 'Rechazar Solicitud',
                'modelo' => 'SolicitudAprobacion',
                'modelo_id' => $solicitud->id,
                'descripcion' => "Rechazó solicitud de {$solicitud->tipo} en {$solicitud->tabla} ID {$solicitud->registro_id}",
                'id_usuario' => $usuario->id,
                'created_at' => now(),
            ]);

            session()->flash('success', 'Solicitud rechazada exitosamente.');
            $this->cargarPendientes();

        } catch (\Exception $e) {
            \Log::error('Error al rechazar solicitud', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Ejecuta la acción de la solicitud aprobada
     *
     * @param SolicitudAprobacion $solicitud
     * @return void
     */
    private function ejecutarAccion($solicitud)
    {
        $modelo = match ($solicitud->tabla) {
            'salida' => \App\Models\Salida::class,
            'traslado' => \App\Models\Traslado::class,
            'compra' => \App\Models\Compra::class,
            'devolucion' => \App\Models\Devolucion::class,
            'categoria' => \App\Models\Categoria::class,
            'proveedor' => \App\Models\Proveedor::class,
            'persona' => \App\Models\Persona::class,
            'bodega' => \App\Models\Bodega::class,
            'tarjeta_responsabilidad' => \App\Models\TarjetaResponsabilidad::class,
            'producto' => \App\Models\Producto::class,
            default => null
        };

        if (!$modelo) {
            throw new \Exception("Modelo no encontrado para tabla: {$solicitud->tabla}");
        }

        $registro = $modelo::find($solicitud->registro_id);

        if (!$registro) {
            throw new \Exception("Registro no encontrado: {$solicitud->tabla} ID {$solicitud->registro_id}");
        }

        switch ($solicitud->tipo) {
            case 'ELIMINACION':
            case 'DESACTIVACION':
                // CRÍTICO: Devolver productos al inventario antes de eliminar
                $this->devolverProductosAlInventario($solicitud->tabla, $registro);

                // Marcar como inactivo
                $registro->activo = false;
                $registro->save();
                break;

            case 'EDICION':
                // Actualizar con los datos de la solicitud
                if (isset($solicitud->datos) && is_array($solicitud->datos)) {
                    $registro->update($solicitud->datos);
                }
                break;

            case 'CREACION':
                // Para creación, los datos ya deberían estar en la solicitud
                // Este caso es más complejo y depende del modelo
                break;
        }
    }

    /**
     * Devuelve los productos al inventario cuando se elimina una transacción
     *
     * @param string $tabla
     * @param mixed $registro
     * @return void
     */
    private function devolverProductosAlInventario($tabla, $registro)
    {
        try {
            switch ($tabla) {
                case 'salida':
                    // Requisición (Salida) - Devolver productos a la bodega
                    $detalles = $registro->detallesSalida;
                    foreach ($detalles as $detalle) {
                        // Incrementar cantidad en lote_bodega
                        $loteBodega = \App\Models\LoteBodega::where('id_lote', $detalle->id_lote)
                            ->where('id_bodega', $registro->id_bodega)
                            ->first();

                        if ($loteBodega) {
                            $loteBodega->cantidad += $detalle->cantidad;
                            $loteBodega->save();
                        }
                    }
                    break;

                case 'traslado':
                    // Traslado/Requisición - Devolver productos a bodega origen
                    $detalles = $registro->detallesTraslado;
                    foreach ($detalles as $detalle) {
                        // Incrementar cantidad en bodega origen
                        $loteBodega = \App\Models\LoteBodega::where('id_lote', $detalle->id_lote)
                            ->where('id_bodega', $registro->id_bodega_origen)
                            ->first();

                        if ($loteBodega) {
                            $loteBodega->cantidad += $detalle->cantidad;
                            $loteBodega->save();
                        }

                        // Decrementar cantidad en bodega destino (si ya se trasladó)
                        $loteBodegaDestino = \App\Models\LoteBodega::where('id_lote', $detalle->id_lote)
                            ->where('id_bodega', $registro->id_bodega_destino)
                            ->first();

                        if ($loteBodegaDestino && $loteBodegaDestino->cantidad >= $detalle->cantidad) {
                            $loteBodegaDestino->cantidad -= $detalle->cantidad;
                            $loteBodegaDestino->save();
                        }
                    }
                    break;

                case 'devolucion':
                    // Devolución - Decrementar en bodega (revertir la devolución)
                    $detalles = $registro->detalles;
                    foreach ($detalles as $detalle) {
                        $loteBodega = \App\Models\LoteBodega::where('id_lote', $detalle->id_lote)
                            ->where('id_bodega', $registro->id_bodega)
                            ->first();

                        if ($loteBodega && $loteBodega->cantidad >= $detalle->cantidad) {
                            $loteBodega->cantidad -= $detalle->cantidad;
                            $loteBodega->save();
                        }
                    }
                    break;
            }

            \Log::info("Productos devueltos al inventario", [
                'tabla' => $tabla,
                'registro_id' => $registro->id
            ]);

        } catch (\Exception $e) {
            \Log::error("Error al devolver productos al inventario", [
                'tabla' => $tabla,
                'registro_id' => $registro->id,
                'error' => $e->getMessage()
            ]);
            // No lanzar excepción para no bloquear la eliminación
        }
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.aprobaciones-pendientes');
    }
}
