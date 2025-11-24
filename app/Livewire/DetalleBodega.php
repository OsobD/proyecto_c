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

    protected $paginationTheme = 'tailwind';

    public function mount($id)
    {
        $this->bodegaId = $id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoriaId()
    {
        $this->resetPage();
    }

    public function updatingEstado()
    {
        $this->resetPage();
    }

    public function render()
    {
        $bodega = Bodega::findOrFail($this->bodegaId);

        $query = Lote::with(['producto.categoria'])
            ->where('id_bodega', $this->bodegaId);

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

        // Filtro por estado
        if ($this->estado !== '') {
            $query->where('estado', $this->estado == '1');
        }

        $lotes = $query->orderBy('fecha_ingreso', 'desc')->paginate(15);
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();

        return view('livewire.detalle-bodega', [
            'bodega' => $bodega,
            'lotes' => $lotes,
            'categorias' => $categorias
        ]);
    }
}
