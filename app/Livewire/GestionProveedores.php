<?php

namespace App\Livewire;

use Livewire\Component;

class GestionProveedores extends Component
{
    public $proveedores = [];

    public function mount()
    {
        $this->proveedores = [
            ['id' => 1, 'nombre' => 'Ferretería El Martillo Feliz', 'contacto' => 'contacto@martillo.com', 'estado' => 'Activo'],
            ['id' => 2, 'nombre' => 'Suministros Industriales S.A.', 'contacto' => 'ventas@suministros.com', 'estado' => 'Activo'],
            ['id' => 3, 'nombre' => 'Oficina Total', 'contacto' => 'info@ofitotal.com', 'estado' => 'Inactivo'],
        ];
    }

    public function render()
    {
        return view('livewire.gestion-proveedores');
    }
}
