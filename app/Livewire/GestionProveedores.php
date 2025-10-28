<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Componente GestionProveedores
 *
 * Gestiona el listado de proveedores del sistema de inventario.
 * Muestra información básica de cada proveedor (nombre, contacto, estado).
 *
 * @package App\Livewire
 * @see resources/views/livewire/gestion-proveedores.blade.php
 */
class GestionProveedores extends Component
{
    /** @var array Listado de proveedores */
    public $proveedores = [];

    /**
     * Inicializa el componente con datos mock de prueba
     *
     * @todo Reemplazar con consultas a BD: Proveedor::all()
     * @return void
     */
    public function mount()
    {
        $this->proveedores = [
            ['id' => 1, 'nombre' => 'Ferretería El Martillo Feliz', 'contacto' => 'contacto@martillo.com', 'estado' => 'Activo'],
            ['id' => 2, 'nombre' => 'Suministros Industriales S.A.', 'contacto' => 'ventas@suministros.com', 'estado' => 'Activo'],
            ['id' => 3, 'nombre' => 'Oficina Total', 'contacto' => 'info@ofitotal.com', 'estado' => 'Inactivo'],
        ];
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.gestion-proveedores');
    }
}
