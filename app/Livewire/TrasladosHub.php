<?php

namespace App\Livewire;

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

    /**
     * Inicializa el componente con datos mock
     *
     * @todo Conectar con BD: Traslado::where('mes', now()->month)->get()
     * @return void
     */
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
