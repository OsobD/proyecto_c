<?php

namespace App\Livewire;

use App\Models\Bodega;
use App\Models\Categoria;
use App\Models\Lote;
use Livewire\Component;
use Livewire\WithPagination;

class DetalleBodega extends Component
{
    use WithPagination;

    public $bodegaId;
    public $search = '';
    public $categoriaId = '';
    public $estado = '';

    // Filtros y Ordenamiento
    public $showFilterModal = false;
    public $sortField = 'fecha_ingreso';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind';

    public function mount($id)
    {
        $this->bodegaId = $id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openFilterModal()
    {
        $this->showFilterModal = true;
    }

    public function closeFilterModal()
    {
        $this->showFilterModal = false;
    }

    public function clearFilters()
    {
        $this->categoriaId = '';
        $this->estado = '';
        $this->tipoProducto = '';
        $this->search = '';
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public $tipoProducto = ''; // '' = Todos, 'consumible' = Consumibles, 'activo' = Activos (No consumibles)

    public function render()
    {
        $bodega = Bodega::findOrFail($this->bodegaId);

        // NUEVA ESTRUCTURA: Consultar desde lote_bodega
        $query = \DB::table('lote_bodega as lb')
            ->join('lote as l', 'lb.id_lote', '=', 'l.id')
            ->join('producto as p', 'l.id_producto', '=', 'p.id')
            ->leftJoin('categoria as c', 'p.id_categoria', '=', 'c.id')
            ->where('lb.id_bodega', $this->bodegaId)
            ->where('lb.cantidad', '>', 0); // Solo mostrar ubicaciones con stock

        // Filtro por búsqueda (código de producto o nombre de producto)
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('p.id', 'like', '%' . $this->search . '%')
                  ->orWhere('p.descripcion', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por categoría
        if (!empty($this->categoriaId)) {
            $query->where('p.id_categoria', $this->categoriaId);
        }

        // Filtro por tipo de producto (Consumible / No Consumible)
        if ($this->tipoProducto !== '') {
            $esConsumible = $this->tipoProducto === 'consumible';
            $query->where('p.es_consumible', $esConsumible);
        }

        // Filtro por estado del lote
        if ($this->estado !== '') {
            $query->where('l.estado', $this->estado == '1');
        }

        // Ordenamiento
        if ($this->sortField === 'producto_id') {
            $query->orderBy('p.id', $this->sortDirection);
        } elseif ($this->sortField === 'producto_descripcion') {
            $query->orderBy('p.descripcion', $this->sortDirection);
        } elseif ($this->sortField === 'cantidad') {
            $query->orderBy('lb.cantidad', $this->sortDirection);
        } elseif ($this->sortField === 'id') {
            $query->orderBy('l.id', $this->sortDirection);
        } elseif ($this->sortField === 'fecha_ingreso') {
            $query->orderBy('l.fecha_ingreso', $this->sortDirection);
        } elseif ($this->sortField === 'precio_ingreso') {
            $query->orderBy('l.precio_ingreso', $this->sortDirection);
        } else {
            $query->orderBy('l.fecha_ingreso', 'desc');
        }

        // Seleccionar columnas necesarias
        $query->select(
            'lb.id as lote_bodega_id',
            'l.id',
            'l.fecha_ingreso',
            'l.precio_ingreso',
            'l.observaciones',
            'l.estado',
            'lb.cantidad',
            'p.id as producto_id',
            'p.descripcion as producto_descripcion',
            'p.es_consumible',
            'c.id as categoria_id',
            'c.nombre as categoria_nombre'
        );

        // Calcular total antes de paginar
        $totalPrecio = $query->get()->sum(function($lote) {
            return $lote->cantidad * $lote->precio_ingreso;
        });

        $lotes = $query->paginate(15);
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();

        return view('livewire.detalle-bodega', [
            'bodega' => $bodega,
            'lotes' => $lotes,
            'categorias' => $categorias,
            'totalPrecio' => $totalPrecio
        ]);
    }
}
