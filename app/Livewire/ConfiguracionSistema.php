<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Componente ConfiguracionSistema
 *
 * Permite configurar parámetros globales del sistema como el porcentaje de IVA,
 * umbrales de stock, y otras configuraciones administrativas.
 *
 * @package App\Livewire
 * @see resources/views/livewire/configuracion-sistema.blade.php
 */
class ConfiguracionSistema extends Component
{
    /** @var float Porcentaje de IVA aplicable (Guatemala: 12%) */
    public $iva = 12.0;

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.configuracion-sistema');
    }
}
