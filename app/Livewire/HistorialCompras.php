<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Traits\TienePermisos;

class HistorialCompras extends Component
{
    use WithPagination, TienePermisos;

    public $search = '';
    public $fechaInicio = '';
    public $fechaFin = '';
    public $estadoFiltro = '';
    public $proveedorFiltro = '';

    public $compras = [];
    public $proveedores = [];

    // Para edición/desactivación
    public $showModalEditar = false;
    public $compraSeleccionada = null;

    public function mount()
    {
        $this->proveedores = [
            ['id' => 1, 'nombre' => 'Ferretería El Martillo Feliz'],
            ['id' => 2, 'nombre' => 'Suministros Industriales S.A.'],
            ['id' => 3, 'nombre' => 'Distribuidora García'],
        ];

        $this->compras = [
            [
                'id' => 1,
                'numero_factura' => 'FAC-001',
                'numero_serie' => 'A',
                'proveedor' => 'Ferretería El Martillo Feliz',
                'proveedor_id' => 1,
                'fecha' => '2025-10-18',
                'monto' => 5250.00,
                'estado' => 'Completada',
                'activa' => true,
                'productos_count' => 5,
            ],
            [
                'id' => 2,
                'numero_factura' => 'FAC-002',
                'numero_serie' => 'B',
                'proveedor' => 'Suministros Industriales S.A.',
                'proveedor_id' => 2,
                'fecha' => '2025-10-17',
                'monto' => 12800.00,
                'estado' => 'Pendiente',
                'activa' => true,
                'productos_count' => 8,
            ],
            [
                'id' => 3,
                'numero_factura' => 'FAC-003',
                'numero_serie' => 'A',
                'proveedor' => 'Distribuidora García',
                'proveedor_id' => 3,
                'fecha' => '2025-10-16',
                'monto' => 8450.00,
                'estado' => 'Completada',
                'activa' => true,
                'productos_count' => 3,
            ],
            [
                'id' => 4,
                'numero_factura' => 'FAC-004',
                'numero_serie' => 'C',
                'proveedor' => 'Ferretería El Martillo Feliz',
                'proveedor_id' => 1,
                'fecha' => '2025-10-15',
                'monto' => 3200.00,
                'estado' => 'Completada',
                'activa' => false,
                'productos_count' => 2,
            ],
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->search = '';
        $this->fechaInicio = '';
        $this->fechaFin = '';
        $this->estadoFiltro = '';
        $this->proveedorFiltro = '';
        $this->resetPage();
    }

    public function getComprasFiltradas()
    {
        $compras = $this->compras;

        // Filtro por búsqueda
        if ($this->search) {
            $search = strtolower($this->search);
            $compras = array_filter($compras, function($compra) use ($search) {
                return str_contains(strtolower($compra['numero_factura']), $search) ||
                       str_contains(strtolower($compra['proveedor']), $search);
            });
        }

        // Filtro por proveedor
        if ($this->proveedorFiltro) {
            $compras = array_filter($compras, function($compra) {
                return $compra['proveedor_id'] == $this->proveedorFiltro;
            });
        }

        // Filtro por estado
        if ($this->estadoFiltro) {
            $compras = array_filter($compras, function($compra) {
                return $compra['estado'] === $this->estadoFiltro;
            });
        }

        // Filtro por fecha
        if ($this->fechaInicio) {
            $compras = array_filter($compras, function($compra) {
                return $compra['fecha'] >= $this->fechaInicio;
            });
        }

        if ($this->fechaFin) {
            $compras = array_filter($compras, function($compra) {
                return $compra['fecha'] <= $this->fechaFin;
            });
        }

        return array_values($compras);
    }

    public function verDetalle($compraId)
    {
        // Aquí iría la lógica para mostrar el detalle
        session()->flash('message', 'Mostrando detalle de compra #' . $compraId);
    }

    public function editarCompra($compraId)
    {
        if (!$this->verificarPermiso('compras.editar', 'Solo supervisores pueden editar compras.')) {
            return;
        }

        $this->compraSeleccionada = collect($this->compras)->firstWhere('id', $compraId);
        $this->showModalEditar = true;
    }

    public function desactivarCompra($compraId)
    {
        if (!$this->verificarPermiso('compras.desactivar', 'Solo supervisores pueden desactivar compras.')) {
            return;
        }

        $key = array_search($compraId, array_column($this->compras, 'id'));
        if ($key !== false) {
            $this->compras[$key]['activa'] = false;
            session()->flash('message', 'Compra desactivada exitosamente.');
        }
    }

    public function activarCompra($compraId)
    {
        $key = array_search($compraId, array_column($this->compras, 'id'));
        if ($key !== false) {
            $this->compras[$key]['activa'] = true;
            session()->flash('message', 'Compra activada exitosamente.');
        }
    }

    public function closeModalEditar()
    {
        $this->showModalEditar = false;
        $this->compraSeleccionada = null;
    }

    public function render()
    {
        return view('livewire.historial-compras', [
            'comprasFiltradas' => $this->getComprasFiltradas(),
        ]);
    }
}
