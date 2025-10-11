<?php

namespace App\Livewire;

use Livewire\Component;

class GestionUsuarios extends Component
{
    public $usuarios = [];

    public function mount()
    {
        $this->usuarios = [
            ['id' => 1, 'nombre' => 'David Bautista', 'email' => 'admin@eemq.com', 'rol' => 'Administrador TI'],
            ['id' => 2, 'nombre' => 'Juan Pérez', 'email' => 'jperez@eemq.com', 'rol' => 'Colaborador'],
            ['id' => 3, 'nombre' => 'María García', 'email' => 'mgarcia@eemq.com', 'rol' => 'Jefe de Bodega'],
        ];
    }

    public function render()
    {
        return view('livewire.gestion-usuarios');
    }
}
