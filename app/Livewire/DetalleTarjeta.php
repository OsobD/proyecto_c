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

    protected $paginationTheme = 'tailwind';

    public function mount($id)
    {
        $this->tarjetaId = $id;
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
        $tarjeta = TarjetaResponsabilidad::with('persona')->findOrFail($this->tarjetaId);

        $query = TarjetaProducto::with(['producto.categoria', 'lote.bodega'])
            ->where('id_tarjeta', $this->tarjetaId)
            ->whereHas('producto', function($q) {
                $q->where('es_consumible', 0);
            });

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

        // Filtro por estado del lote
        if ($this->estado !== '') {
            $query->whereHas('lote', function($q) {
                $q->where('estado', $this->estado == '1');
            });
        }

        $activos = $query->latest()->paginate(15);
        $categorias = Categoria::where('activo', true)->orderBy('nombre')->get();

        return view('livewire.detalle-tarjeta', [
            'tarjeta' => $tarjeta,
            'activos' => $activos,
            'categorias' => $categorias
        ]);
    }
}
