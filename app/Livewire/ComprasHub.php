<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class ComprasHub
 * @package App\Livewire
 * @brief Componente principal para la gestión de compras.
 *
 * Este componente actúa como un panel de control para el módulo de compras,
 * mostrando estadísticas clave y una lista de las compras más recientes.
 * Actualmente, utiliza datos de ejemplo.
 */
class ComprasHub extends Component
{
    /**
     * @var array
     * @brief Almacena estadísticas relevantes del módulo de compras.
     *
     * Incluye total de compras del mes, monto total, compras pendientes y
     * el número de proveedores activos.
     */
    public $estadisticas = [];

    /**
     * @var array
     * @brief Almacena una lista de las compras registradas recientemente.
     *
     * Cada compra es un array con 'id', 'numero_factura', 'proveedor',
     * 'fecha', 'monto' y 'estado'.
     */
    public $comprasRecientes = [];

    /**
     * @brief Método que se ejecuta al inicializar el componente.
     *
     * Carga datos de ejemplo para las estadísticas y la lista de compras
     * recientes. En el futuro, debería obtener estos datos de la base de datos.
     *
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

    /**
     * @brief Renderiza la vista del componente.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.compras-hub');
    }
}
