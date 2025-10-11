<?php

namespace App\Livewire;

use Livewire\Component;

class ConfiguracionSistema extends Component
{
    public $iva = 12.0;

    public function render()
    {
        return view('livewire.configuracion-sistema');
    }
}
