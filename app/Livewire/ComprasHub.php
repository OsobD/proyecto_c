<?php

namespace App\Livewire;

use Livewire\Component;

class ComprasHub extends Component
{
    public $estadisticas = [];
    public $comprasRecientes = [];

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
