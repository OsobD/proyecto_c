<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class BitacoraSistema
 * @package App\Livewire
 * @brief Componente de Livewire para gestionar y mostrar la bitácora del sistema.
 *
 * Este componente se encarga de obtener y presentar los registros de actividad
 * (logs) del sistema. Actualmente, utiliza datos de ejemplo para la visualización.
 */
class BitacoraSistema extends Component
{
    /**
     * @var array
     * @brief Almacena los registros de la bitácora del sistema.
     *
     * Cada registro es un array asociativo con 'fecha', 'usuario', 'accion' y 'descripcion'.
     */
    public $logs = [];

    /**
     * @brief Método que se ejecuta al inicializar el componente.
     *
     * Carga los datos de ejemplo en la propiedad $logs. En una implementación
     * futura, este método debería obtener los registros desde la base de datos.
     *
     * @return void
     */
    public function mount()
    {
        // Datos de ejemplo para la bitácora
        $this->logs = [
            [
                'fecha' => '2023-10-26 10:00:00',
                'usuario' => 'Juan Pérez',
                'accion' => 'Creación de Requisición',
                'descripcion' => 'Se creó la requisición #123 para 50 unidades de Tornillos de acero.'
            ],
            [
                'fecha' => '2023-10-26 09:30:00',
                'usuario' => 'admin@eemq.com',
                'accion' => 'Inicio de Sesión',
                'descripcion' => 'El usuario inició sesión exitosamente.'
            ],
            [
                'fecha' => '2023-10-25 15:00:00',
                'usuario' => 'María García',
                'accion' => 'Registro de Compra',
                'descripcion' => 'Se registró la compra #45 de 100 unidades de Cinta aislante.'
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
        return view('livewire.bitacora-sistema');
    }
}
