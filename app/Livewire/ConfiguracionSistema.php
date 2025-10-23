<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class ConfiguracionSistema
 * @package App\Livewire
 * @brief Componente para gestionar la configuración general del sistema.
 *
 * Este componente permite a los administradores ajustar parámetros clave
 * del sistema, como el valor del Impuesto al Valor Agregado (IVA).
 */
class ConfiguracionSistema extends Component
{
    /**
     * @var float
     * @brief Porcentaje del Impuesto al Valor Agregado (IVA).
     *
     * Este valor se utiliza para los cálculos de impuestos en los módulos
     * de compras y ventas. El valor se representa como un porcentaje, por
     * ejemplo, 12.0 para el 12%.
     */
    public $iva = 12.0;

    /**
     * @brief Renderiza la vista del componente.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.configuracion-sistema');
    }
}
