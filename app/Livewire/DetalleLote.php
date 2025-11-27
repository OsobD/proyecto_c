<?php

namespace App\Livewire;

use App\Models\Lote;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

/**
 * Componente DetalleLote
 *
 * Muestra las ubicaciones de un lote específico, incluyendo:
 * - Bodegas donde está ubicado con sus cantidades
 * - Tarjetas de responsabilidad asignadas con sus cantidades
 * - Información general del lote
 */
class DetalleLote extends Component
{
    use WithPagination;

    public $loteId;
    public $search = '';
    public $tipoUbicacion = ''; // '' = Todos, 'bodega' = Solo bodegas, 'tarjeta' = Solo tarjetas

    // Filtros y Ordenamiento
    public $showFilterModal = false;
    public $sortField = 'nombre';
    public $sortDirection = 'asc';

    protected $paginationTheme = 'tailwind';

    public function mount($id)
    {
        $this->loteId = $id;
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
        $this->tipoUbicacion = '';
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

    public function render()
    {
        $lote = Lote::with('producto')->findOrFail($this->loteId);

        // Obtener ubicaciones en bodegas
        $bodegas = DB::table('lote_bodega as lb')
            ->join('bodega as b', 'lb.id_bodega', '=', 'b.id')
            ->where('lb.id_lote', $this->loteId)
            ->where('lb.cantidad', '>', 0)
            ->select(
                'b.id',
                'b.nombre',
                'lb.cantidad',
                DB::raw("'bodega' as tipo")
            );

        // Obtener ubicaciones en tarjetas
        $tarjetas = DB::table('producto_tarjeta as pt')
            ->join('tarjeta_responsabilidad as tr', 'pt.id_tarjeta', '=', 'tr.id')
            ->join('persona as p', 'tr.id_persona', '=', 'p.id')
            ->where('pt.id_lote', $this->loteId)
            ->where('pt.cantidad', '>', 0)
            ->select(
                'tr.id',
                DB::raw("CONCAT(p.nombres, ' ', p.apellidos, ' (Tarjeta #', tr.id, ')') as nombre"),
                'pt.cantidad',
                DB::raw("'tarjeta' as tipo")
            );

        // Unir ambas consultas
        $query = $bodegas->union($tarjetas);

        // Aplicar filtro de búsqueda
        if (!empty($this->search)) {
            $query = DB::table(DB::raw("({$query->toSql()}) as ubicaciones"))
                ->mergeBindings($query)
                ->where('nombre', 'like', '%' . $this->search . '%');
        } else {
            $query = DB::table(DB::raw("({$query->toSql()}) as ubicaciones"))
                ->mergeBindings($query);
        }

        // Aplicar filtro de tipo
        if ($this->tipoUbicacion !== '') {
            $query->where('tipo', $this->tipoUbicacion);
        }

        // Ordenamiento
        $query->orderBy($this->sortField, $this->sortDirection);

        $ubicaciones = $query->paginate(15);

        // Calcular totales
        $totalBodegas = DB::table('lote_bodega')
            ->where('id_lote', $this->loteId)
            ->where('cantidad', '>', 0)
            ->count();

        $totalTarjetas = DB::table('producto_tarjeta')
            ->where('id_lote', $this->loteId)
            ->where('cantidad', '>', 0)
            ->count();

        $cantidadEnBodegas = DB::table('lote_bodega')
            ->where('id_lote', $this->loteId)
            ->sum('cantidad');

        $cantidadEnTarjetas = DB::table('producto_tarjeta')
            ->where('id_lote', $this->loteId)
            ->sum('cantidad');

        return view('livewire.detalle-lote', [
            'lote' => $lote,
            'ubicaciones' => $ubicaciones,
            'totalBodegas' => $totalBodegas,
            'totalTarjetas' => $totalTarjetas,
            'cantidadEnBodegas' => $cantidadEnBodegas,
            'cantidadEnTarjetas' => $cantidadEnTarjetas,
        ]);
    }
}
