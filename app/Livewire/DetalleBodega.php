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

        $query = Lote::with(['producto.categoria', 'bodega'])
            ->where('id_bodega', $this->bodegaId)
            ->whereHas('producto'); // Asegurar que tenga producto asociado

        // Filtro por búsqueda (código de producto o nombre de producto)
        if (!empty($this->search)) {
            $query->whereHas('producto', function($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por categoría
        if (!empty($this->categoriaId)) {
            $query->whereHas('producto', function($q) {
                $q->where('id_categoria', $this->categoriaId);
            });
        }

        // Filtro por tipo de producto (Consumible / No Consumible)
        if ($this->tipoProducto !== '') {
            $esConsumible = $this->tipoProducto === 'consumible';
            $query->whereHas('producto', function($q) use ($esConsumible) {
                $q->where('es_consumible', $esConsumible);
            });
        }

        // Filtro por estado del lote
        if ($this->estado !== '') {
            $query->where('estado', $this->estado == '1');
        }

        // Ordenamiento
        if ($this->sortField === 'producto_id') {
            $query->join('producto', 'lote.id_producto', '=', 'producto.id')
                  ->orderBy('producto.id', $this->sortDirection)
                  ->select('lote.*'); // Evitar conflictos de columnas
        } elseif ($this->sortField === 'producto_descripcion') {
            $query->join('producto', 'lote.id_producto', '=', 'producto.id')
                  ->orderBy('producto.descripcion', $this->sortDirection)
                  ->select('lote.*');
        } elseif ($this->sortField === 'cantidad') {
             $query->orderBy('cantidad', $this->sortDirection);
        } else {
            // Asegurar que el campo de ordenamiento exista en la tabla lote
            if (in_array($this->sortField, ['id', 'fecha_ingreso', 'precio_ingreso', 'estado'])) {
                $query->orderBy($this->sortField, $this->sortDirection);
            } else {
                $query->orderBy('fecha_ingreso', 'desc');
            }
        }

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
