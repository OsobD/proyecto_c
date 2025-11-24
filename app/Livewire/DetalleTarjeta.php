<?php

namespace App\Livewire;

use App\Models\TarjetaResponsabilidad;
use App\Models\TarjetaProducto;
use App\Models\Categoria;
use Livewire\Component;
use Livewire\WithPagination;

class DetalleTarjeta extends Component
{
    use WithPagination;

    public $tarjetaId;
    public $search = '';
    public $categoriaId = '';
    public $estado = '';

    // Filtros y Ordenamiento
    public $showFilterModal = false;
    public $sortField = 'id';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind';

    public function mount($id)
    {
        $this->tarjetaId = $id;
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
        $tarjeta = TarjetaResponsabilidad::with('persona')->findOrFail($this->tarjetaId);

        $query = TarjetaProducto::with(['producto.categoria', 'lote.bodega'])
            ->where('id_tarjeta', $this->tarjetaId)
            ->whereHas('producto');

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
            $query->whereHas('lote', function($q) {
                $q->where('estado', $this->estado == '1');
            });
        }

        // Ordenamiento
        if ($this->sortField === 'producto_id') {
            $query->join('producto', 'tarjeta_producto.id_producto', '=', 'producto.id')
                  ->orderBy('producto.id', $this->sortDirection)
                  ->select('tarjeta_producto.*');
        } elseif ($this->sortField === 'producto_descripcion') {
            $query->join('producto', 'tarjeta_producto.id_producto', '=', 'producto.id')
                  ->orderBy('producto.descripcion', $this->sortDirection)
                  ->select('tarjeta_producto.*');
        } else {
            // Asegurar que el campo de ordenamiento sea válido
            if (in_array($this->sortField, ['id', 'precio_asignacion', 'id_lote'])) {
                 $query->orderBy($this->sortField, $this->sortDirection);
            } else {
                 $query->orderBy('id', 'desc');
            }
        }

        $activos = $query->paginate(15);
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();

        return view('livewire.detalle-tarjeta', [
            'tarjeta' => $tarjeta,
            'activos' => $activos,
            'categorias' => $categorias
        ]);
    }
}
