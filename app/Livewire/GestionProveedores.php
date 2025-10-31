<?php

namespace App\Livewire;

use App\Models\Proveedor;
use Livewire\Component;

/**
 * Componente GestionProveedores
 *
 * Gestiona el listado de proveedores del sistema de inventario.
 * Muestra información básica de cada proveedor desde la base de datos.
 * Por ahora es solo visualización, el CRUD completo se implementará después.
 *
 * @package App\Livewire
 * @see resources/views/livewire/gestion-proveedores.blade.php
 */
class GestionProveedores extends Component
{
    /**
     * Computed property: Retorna todos los proveedores desde BD
     *
     * @return array Listado de proveedores con información básica
     */
    public function getProveedoresProperty()
    {
        return Proveedor::with('regimenTributario')
            ->orderBy('nombre')
            ->get()
            ->map(fn($proveedor) => [
                'id' => $proveedor->id,
                'nombre' => $proveedor->nombre,
                'nit' => $proveedor->nit,
                'regimen' => $proveedor->regimenTributario->nombre ?? 'N/A',
                'activo' => $proveedor->activo,
            ])
            ->toArray();
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
