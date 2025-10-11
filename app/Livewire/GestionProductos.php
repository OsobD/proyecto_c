<?php

namespace App\Livewire;

use Livewire\Component;

class GestionProductos extends Component
{
    public $productos = [];

    public function mount()
    {
        $this->productos = [
            ['codigo' => 'PROD-001', 'descripcion' => 'Tornillos de acero inoxidable', 'costo' => '15.50'],
            ['codigo' => 'PROD-002', 'descripcion' => 'Abrazaderas de metal', 'costo' => '5.75'],
            ['codigo' => 'PROD-003', 'descripcion' => 'Cinta aislante', 'costo' => '3.00'],
            ['codigo' => 'PROD-004', 'descripcion' => 'Guantes de seguridad', 'costo' => '12.00'],
            ['codigo' => 'PROD-005', 'descripcion' => 'Fusibles de 15A', 'costo' => '8.25'],
        ];
    }

    public function render()
    {
        return view('livewire.gestion-productos');
    }
}
