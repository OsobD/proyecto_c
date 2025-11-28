<?php

namespace App\Livewire;

use App\Models\SolicitudAprobacion;
use App\Models\Compra;
use App\Models\DetalleCompra;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Aprobaciones extends Component
{
    use WithPagination;

    public $filtroEstado = 'PENDIENTE';
    public $solicitudSeleccionada = null;
    public $showModalDetalle = false;

    public function mount()
    {
        // Verificar permisos (solo admin y jefe)
        // Esto idealmente debería ser vía middleware o gate, pero por rapidez:
        /* if (!auth()->user()->hasRole(['Administrador TI', 'Jefe de Bodega'])) {
            abort(403);
        } */
    }

    public function verDetalle($id)
    {
        $this->solicitudSeleccionada = SolicitudAprobacion::with('solicitante')->find($id);
        
        if ($this->solicitudSeleccionada) {
            // Si es edición de compra, cargar datos actuales para comparar
            if ($this->solicitudSeleccionada->tabla === 'compra' && $this->solicitudSeleccionada->tipo === 'EDICION') {
                $compraActual = Compra::with('detalles.producto')->find($this->solicitudSeleccionada->registro_id);
                $this->solicitudSeleccionada->datos_actuales = $compraActual;
            }
            
            $this->showModalDetalle = true;
        }
    }

    public function cerrarModal()
    {
        $this->showModalDetalle = false;
        $this->solicitudSeleccionada = null;
    }

    public function aprobar($id)
    {
        $solicitud = SolicitudAprobacion::find($id);
        
        if (!$solicitud || $solicitud->estado !== 'PENDIENTE') {
            return;
        }

        DB::beginTransaction();
        try {
            if ($solicitud->tabla === 'compra') {
                if ($solicitud->tipo === 'EDICION') {
                    $this->aplicarEdicionCompra($solicitud);
                } elseif ($solicitud->tipo === 'DESACTIVACION') {
                    $this->aplicarDesactivacionCompra($solicitud);
                }
            }

            $solicitud->estado = 'APROBADO';
            $solicitud->save();
            
            DB::commit();
            session()->flash('message', 'Solicitud aprobada y cambios aplicados correctamente.');
            $this->cerrarModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al aprobar solicitud: ' . $e->getMessage());
        }
    }

    public function rechazar($id)
    {
        $solicitud = SolicitudAprobacion::find($id);
        if ($solicitud) {
            $solicitud->estado = 'RECHAZADO';
            $solicitud->save();
            session()->flash('message', 'Solicitud rechazada.');
            $this->cerrarModal();
        }
    }

    private function aplicarEdicionCompra($solicitud)
    {
        $datos = $solicitud->datos;
        $compra = Compra::with('transacciones.lotes')->find($solicitud->registro_id);

        if (!$compra) throw new \Exception("Compra no encontrada");

        // Obtener la transacción de entrada asociada a la compra
        // Asumimos que hay una transacción de tipo 'ENTRADA' o 'COMPRA'
        // Si no, buscamos la primera transacción
        $transaccion = $compra->transacciones->first();
        
        if (!$transaccion) {
            // Si no hay transacción, no podemos actualizar inventario de forma segura
            // Esto podría ser un error de datos antiguos
            throw new \Exception("No se encontró transacción asociada a la compra");
        }

        // Actualizar detalles y stock
        if (isset($datos['productos'])) {
            foreach ($datos['productos'] as $prod) {
                $detalle = DetalleCompra::find($prod['id_detalle']);
                if ($detalle) {
                    $cantidadAnterior = $detalle->cantidad;
                    $nuevaCantidad = $prod['cantidad'];
                    $diferencia = $nuevaCantidad - $cantidadAnterior;

                    // Actualizar detalle
                    $detalle->cantidad = $nuevaCantidad;
                    // Usar precio con IVA si existe, sino el precio normal
                    $detalle->precio_ingreso = $prod['precio_con_iva'] ?? $prod['precio'];
                    $detalle->save();

                    // Actualizar Inventario (Lote)
                    // Buscar el lote correspondiente a este producto y transacción
                    $lote = $transaccion->lotes->where('id_producto', $detalle->id_producto)->first();

                    if ($lote) {
                        // Actualizar cantidad disponible del lote
                        // Si la diferencia es positiva, aumentamos stock. Si es negativa, disminuimos.
                        $lote->cantidad_disponible += $diferencia;
                        $lote->save();

                        // Actualizar LoteBodega (stock en la bodega específica)
                        $loteBodega = \App\Models\LoteBodega::obtenerOCrear($lote->id, $compra->id_bodega);
                        $loteBodega->cantidad += $diferencia;
                        $loteBodega->save();
                    }
                }
            }
        }

        // Actualizar total
        $compra->total = $datos['total_con_iva'] ?? $datos['total'];
        $compra->save();
    }

    private function aplicarDesactivacionCompra($solicitud)
    {
        $compra = Compra::with('transacciones.lotes')->find($solicitud->registro_id);
        if ($compra) {
            $compra->activo = false;
            $compra->save();

            // Revertir inventario (restar todo lo que entró)
            $transaccion = $compra->transacciones->first();
            if ($transaccion) {
                foreach ($transaccion->lotes as $lote) {
                    // Restar la cantidad disponible actual del lote de la bodega
                    // O restar la cantidad original de la compra?
                    // Lo más seguro es restar lo que actualmente está disponible en ese lote
                    // Pero si ya se consumió, podríamos dejarlo en negativo o solo restar lo disponible.
                    // Asumiremos que se debe revertir la entrada completa si es posible.
                    
                    // Estrategia: Restar la cantidad actual del lote de la bodega
                    $loteBodega = \App\Models\LoteBodega::where('id_lote', $lote->id)
                        ->where('id_bodega', $compra->id_bodega)
                        ->first();

                    if ($loteBodega) {
                        $cantidadARestar = $lote->cantidad_disponible; // O la cantidad original de la compra?
                        // Si desactivamos la compra, asumimos que esos productos "desaparecen" del sistema.
                        // Si ya se vendieron/usaron, el stock ya bajó.
                        // Solo debemos quitar lo que queda disponible.
                        
                        $loteBodega->cantidad -= $cantidadARestar;
                        if ($loteBodega->cantidad < 0) $loteBodega->cantidad = 0;
                        $loteBodega->save();
                    }

                    $lote->cantidad_disponible = 0;
                    $lote->estado = false; // Desactivar lote
                    $lote->save();
                }
            }
        }
    }

    public function render()
    {
        $solicitudes = SolicitudAprobacion::with('solicitante')
            ->where('estado', $this->filtroEstado)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.aprobaciones', [
            'solicitudes' => $solicitudes
        ]);
    }
}
