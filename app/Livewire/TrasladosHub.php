<?php

namespace App\Livewire;

use Livewire\Component;

class TrasladosHub extends Component
{
    public $estadisticas = [];
    public $trasladosRecientes = [];

    public function mount()
    {
        // Estadísticas simuladas
        $this->estadisticas = [
            'requisiciones_mes' => 12,
            'traslados_mes' => 8,
            'devoluciones_mes' => 2,
            'pendientes' => 5,
        ];

        // Traslados recientes simulados
        $this->trasladosRecientes = [
            [
                'id' => 1,
                'tipo' => 'Requisición',
                'correlativo' => 'REQ-001',
                'origen' => 'Bodega Central',
                'destino' => 'Área de Mantenimiento',
                'fecha' => '2025-10-18',
                'estado' => 'Completado',
            ],
            [
                'id' => 2,
                'tipo' => 'Traslado',
                'correlativo' => 'TRA-005',
                'origen' => 'Bodega Norte',
                'destino' => 'Bodega Sur',
                'fecha' => '2025-10-17',
                'estado' => 'Pendiente',
            ],
            [
                'id' => 3,
                'tipo' => 'Devolución',
                'correlativo' => 'DEV-002',
                'origen' => 'Área de Producción',
                'destino' => 'Bodega Central',
                'fecha' => '2025-10-16',
                'estado' => 'Completado',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.traslados-hub');
    }
}
