<?php

namespace App\Livewire;

use Livewire\Component;

class GestionBodegas extends Component
{
    public $bodegas = [];
    public $isModalOpen = false;
    public $nombre;
    public $tipo = null;
    public $showTipoDropdown = false;

    public $tiposDisponibles = [
        ['id' => 1, 'nombre' => 'Física'],
        ['id' => 2, 'nombre' => 'Responsabilidad'],
    ];

    public function mount()
    {
        $this->bodegas = [
            ['id' => 1, 'nombre' => 'Bodega Central', 'tipo' => 'Física'],
            ['id' => 2, 'nombre' => 'Juan Pérez (Tarjeta de Responsabilidad)', 'tipo' => 'Responsabilidad'],
            ['id' => 3, 'nombre' => 'Almacén de Suministros', 'tipo' => 'Física'],
            ['id' => 4, 'nombre' => 'María García (Tarjeta de Responsabilidad)', 'tipo' => 'Responsabilidad'],
            ['id' => 5, 'nombre' => 'Bodega Secundaria', 'tipo' => 'Física'],
        ];
    }

    public function render()
    {
        return view('livewire.gestion-bodegas');
    }

    public function openModal()
    {
        $this->isModalOpen = true;
        $this->showTipoDropdown = false;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->nombre = null;
        $this->tipo = null;
    }

    public function selectTipo($tipo)
    {
        $this->tipo = $tipo;
        $this->showTipoDropdown = false;
    }

    public function clearTipo()
    {
        $this->tipo = null;
    }

    public function saveBodega()
    {
        // Here you would typically validate and save the data.
        // For this UI-only project, we'll just close the modal.
        $this->closeModal();
    }
}
