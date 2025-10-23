<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class TrasladosHub
 * @package App\Livewire
 * @brief Componente principal para la gestión de traslados y movimientos de inventario.
 *
 * Este componente actúa como un panel de control para el módulo de traslados,
 * mostrando estadísticas clave (requisiciones, traslados, devoluciones) y una
 * lista de los movimientos más recientes. Actualmente, utiliza datos de ejemplo.
 */
class TrasladosHub extends Component
{
    /**
     * @var array
     * @brief Almacena estadísticas relevantes del módulo de traslados.
     *
     * Incluye el total de requisiciones, traslados y devoluciones del mes,
     * así como el número de movimientos pendientes.
     */
    public $estadisticas = [];

    /**
     * @var array
     * @brief Almacena una lista de los traslados registrados recientemente.
     *
     * Cada traslado es un array con 'id', 'tipo', 'correlativo', 'origen',
     * 'destino', 'fecha' y 'estado'.
     */
    public $trasladosRecientes = [];

    /**
     * @brief Método que se ejecuta al inicializar el componente.
     *
     * Carga datos de ejemplo para las estadísticas y la lista de traslados
     * recientes. En el futuro, debería obtener estos datos de la base de datos.
     *
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
     * @brief Renderiza la vista del componente.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.traslados-hub');
    }
}
