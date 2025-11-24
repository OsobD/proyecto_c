<?php

namespace App\Livewire;

use App\Models\Bitacora;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente BitacoraSistema
 *
 * Muestra el registro de actividades del sistema (audit log) con información
 * de acciones realizadas por usuarios, timestamps y descripciones detalladas.
 */
class BitacoraSistema extends Component
{
    use WithPagination;

    public $searchUsuario = '';
    public $fechaInicio = '';
    public $fechaFin = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearchUsuario()
    {
        $this->resetPage();
    }

    public function updatingFechaInicio()
    {
        $this->resetPage();
    }

    public function updatingFechaFin()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Bitacora::with('usuario')
            ->orderBy('created_at', 'desc');

        if (!empty($this->searchUsuario)) {
            $query->where(function ($q) {
                $q->whereHas('usuario', function ($subQ) {
                    $subQ->where('nombre_usuario', 'like', '%' . $this->searchUsuario . '%')
                        ->orWhereHas('persona', function ($personaQ) {
                            $personaQ->where('nombres', 'like', '%' . $this->searchUsuario . '%')
                                ->orWhere('apellidos', 'like', '%' . $this->searchUsuario . '%')
                                ->orWhere('correo', 'like', '%' . $this->searchUsuario . '%');
                        });
                });
            });
        }

        if (!empty($this->fechaInicio)) {
            $query->whereDate('created_at', '>=', $this->fechaInicio);
        }

        if (!empty($this->fechaFin)) {
            $query->whereDate('created_at', '<=', $this->fechaFin);
        }

        $bitacoras = $query->paginate(10);

        return view('livewire.bitacora-sistema', [
            'bitacoras' => $bitacoras
        ]);
    }
}
