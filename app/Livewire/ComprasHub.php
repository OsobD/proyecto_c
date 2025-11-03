<?php

namespace App\Livewire;

use App\Models\Compra;
use App\Models\Proveedor;
use Livewire\Component;

/**
 * Componente ComprasHub
 *
 * Dashboard principal del módulo de compras. Muestra estadísticas del mes
 * y un resumen de las compras más recientes.
 *
 * @package App\Livewire
 * @see resources/views/livewire/compras-hub.blade.php
 */
class ComprasHub extends Component
{
    /** @var array Estadísticas del mes actual */
    public $estadisticas = [];

    /** @var array Listado de compras recientes */
    public $comprasRecientes = [];

    /**
     * Inicializa el componente con datos reales de la base de datos
     *
     * @return void
     */
    public function mount()
    {
        // Obtener compras del mes actual
        $comprasMes = Compra::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->get();

        // Calcular estadísticas
        $this->estadisticas = [
            'total_mes' => $comprasMes->count(),
            'monto_total_mes' => $comprasMes->sum('total'),
            'pendientes_revision' => 0, // Puedes ajustar según tu lógica de negocio
            'proveedores_activos' => Proveedor::where('activo', true)->count(),
        ];

        // Obtener las 5 compras más recientes
        $this->comprasRecientes = Compra::with('proveedor')
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get()
            ->map(function($compra) {
                return [
                    'id' => $compra->id,
                    'numero_factura' => $compra->no_factura ?? 'N/A',
                    'proveedor' => $compra->proveedor->nombre ?? 'Sin proveedor',
                    'fecha' => $compra->fecha->format('Y-m-d'),
                    'monto' => $compra->total,
                    'estado' => 'Completada', // Todas las compras guardadas están completadas
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.compras-hub');
    }
}
