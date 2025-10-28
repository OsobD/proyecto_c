<?php

namespace App\Livewire;

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
     * Inicializa el componente con datos mock
     *
     * @todo Conectar con BD: Compra::where('mes', now()->month)->get()
     * @return void
     */
    public function mount()
    {
        // Estadísticas simuladas
        $this->estadisticas = [
            'total_mes' => 15,
            'monto_total_mes' => 125450.50,
            'pendientes_revision' => 3,
            'proveedores_activos' => 8,
        ];

        // Compras recientes simuladas
        $this->comprasRecientes = [
            [
                'id' => 1,
                'numero_factura' => 'FAC-001',
                'proveedor' => 'Ferretería El Martillo Feliz',
                'fecha' => '2025-10-18',
                'monto' => 5250.00,
                'estado' => 'Completada',
            ],
            [
                'id' => 2,
                'numero_factura' => 'FAC-002',
                'proveedor' => 'Suministros Industriales S.A.',
                'fecha' => '2025-10-17',
                'monto' => 12800.00,
                'estado' => 'Pendiente',
            ],
            [
                'id' => 3,
                'numero_factura' => 'FAC-003',
                'proveedor' => 'Distribuidora García',
                'fecha' => '2025-10-16',
                'monto' => 8450.00,
                'estado' => 'Completada',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.compras-hub');
    }
}
