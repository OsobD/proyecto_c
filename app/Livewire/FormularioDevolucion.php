<?php

namespace App\Livewire;

use Livewire\Component;

class FormularioDevolucion extends Component
{
    public $productos = [];

    public function mount()
    {
        $this->productos = [
            ['codigo' => 'PROD-001', 'descripcion' => 'Tornillos de acero inoxidable'],
            ['codigo' => 'PROD-002', 'descripcion' => 'Abrazaderas de metal'],
        ];
    }

    public function render()
    {
        return view('livewire.formulario-devolucion');
    }
}
