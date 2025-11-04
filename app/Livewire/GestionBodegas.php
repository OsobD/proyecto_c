<?php

namespace App\Livewire;

use App\Models\Bitacora;
use App\Models\Bodega;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

/**
 * Componente GestionBodegas
 *
 * Gestiona el CRUD de bodegas físicas del sistema de inventario.
 *
 * @package App\Livewire
 * @see resources/views/livewire/gestion-bodegas.blade.php
 */
class GestionBodegas extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;

    // Campos del formulario
    public $bodegaId;
    public $nombre;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'nombre' => 'required|string|max:255',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre de la bodega es obligatorio.',
        'nombre.max' => 'El nombre no puede exceder los 255 caracteres.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $bodegas = Bodega::where('activo', true)
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('nombre', 'asc')
            ->paginate(10);

        return view('livewire.gestion-bodegas', [
            'bodegas' => $bodegas
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $bodega = Bodega::findOrFail($id);

        $this->bodegaId = $bodega->id;
        $this->nombre = $bodega->nombre;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $bodega = Bodega::findOrFail($this->bodegaId);
                $bodega->update([
                    'nombre' => $this->nombre,
                    'updated_by' => Auth::id(),
                ]);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Actualizar',
                    'descripcion' => "Bodega actualizada: {$bodega->nombre}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                session()->flash('message', 'Bodega actualizada correctamente.');
            } else {
                $bodega = Bodega::create([
                    'nombre' => $this->nombre,
                    'activo' => true,
                    'created_by' => Auth::id(),
                ]);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Crear',
                    'descripcion' => "Bodega creada: {$bodega->nombre}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                session()->flash('message', 'Bodega creada correctamente.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar la bodega: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $bodega = Bodega::with([
            'lotes',
            'compras',
            'entradas',
            'devoluciones',
            'traslados',
            'salidas'
        ])->findOrFail($id);

        // Verificar si tiene relaciones activas
        $tieneLotes = $bodega->lotes()->exists();
        $tieneCompras = $bodega->compras()->exists();
        $tieneEntradas = $bodega->entradas()->exists();
        $tieneDevoluciones = $bodega->devoluciones()->exists();
        $tieneTraslados = $bodega->traslados()->exists();
        $tieneSalidas = $bodega->salidas()->exists();

        if ($tieneLotes || $tieneCompras || $tieneEntradas || $tieneDevoluciones || $tieneTraslados || $tieneSalidas) {
            session()->flash('error', 'No se puede desactivar la bodega porque tiene movimientos asociados (lotes, compras, entradas, salidas, etc.).');
            return;
        }

        $this->bodegaId = $id;
        $this->dispatch('confirm-delete');
    }

    public function delete()
    {
        try {
            $bodega = Bodega::findOrFail($this->bodegaId);

            $bodega->update([
                'activo' => false,
                'updated_by' => Auth::id(),
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Desactivar',
                'descripcion' => "Bodega desactivada: {$bodega->nombre}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Bodega desactivada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al desactivar la bodega: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->bodegaId = null;
        $this->nombre = '';
        $this->resetErrorBag();
    }
}
