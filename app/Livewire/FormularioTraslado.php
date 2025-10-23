<?php

namespace App\Livewire;

use Livewire\Component;

class FormularioTraslado extends Component
{
    public $bodegas = [];
    public $productos = [];
    public $searchOrigen = '';
    public $searchDestino = '';
    public $searchProducto = '';
    public $selectedOrigen = null;
    public $selectedDestino = null;
    public $showOrigenDropdown = false;
    public $showDestinoDropdown = false;
    public $showProductoDropdown = false;
    public $productosSeleccionados = [];
    public $correlativo = '';
    public $observaciones = '';

    public function mount()
    {
        $this->bodegas = [
            ['id' => 1, 'nombre' => 'Bodega Central'],
            ['id' => 2, 'nombre' => 'Bodega Norte'],
            ['id' => 3, 'nombre' => 'Bodega Sur'],
        ];

        $this->productos = [
            ['id' => 0xA1, 'descripcion' => 'Tornillos de acero inoxidable', 'precio' => 0.50],
            ['id' => 0xB2, 'descripcion' => 'Abrazaderas de metal', 'precio' => 2.75],
            ['id' => 0xC3, 'descripcion' => 'Cinta aislante', 'precio' => 3.25],
            ['id' => 0xD4, 'descripcion' => 'Guantes de seguridad', 'precio' => 8.50],
            ['id' => 0xE5, 'descripcion' => 'Fusibles de 15A', 'precio' => 1.25],
        ];

        $this->productosSeleccionados = [];
    }

    public function getOrigenResultsProperty()
    {
        $results = [];

        // Only show bodegas for traslados (Bodega -> Bodega)
        foreach ($this->bodegas as $bodega) {
            if (empty($this->searchOrigen) ||
                str_contains(strtolower($bodega['nombre']), strtolower($this->searchOrigen))) {
                $results[] = [
                    'id' => 'B' . $bodega['id'],
                    'nombre' => $bodega['nombre'],
                    'tipo' => 'Bodega'
                ];
            }
        }

        return $results;
    }

    public function getDestinoResultsProperty()
    {
        $results = [];

        // Only show bodegas for traslados (Bodega -> Bodega)
        foreach ($this->bodegas as $bodega) {
            if (empty($this->searchDestino) ||
                str_contains(strtolower($bodega['nombre']), strtolower($this->searchDestino))) {
                $results[] = [
                    'id' => 'B' . $bodega['id'],
                    'nombre' => $bodega['nombre'],
                    'tipo' => 'Bodega'
                ];
            }
        }

        return $results;
    }

    public function selectOrigen($id, $nombre, $tipo)
    {
        $this->selectedOrigen = [
            'id' => $id,
            'nombre' => $nombre,
            'tipo' => $tipo
        ];
        $this->searchOrigen = '';
        $this->showOrigenDropdown = false;
    }

    public function selectDestino($id, $nombre, $tipo)
    {
        $this->selectedDestino = [
            'id' => $id,
            'nombre' => $nombre,
            'tipo' => $tipo
        ];
        $this->searchDestino = '';
        $this->showDestinoDropdown = false;
    }

    public function clearOrigen()
    {
        $this->selectedOrigen = null;
        $this->searchOrigen = '';
        $this->showOrigenDropdown = false;
    }

    public function clearDestino()
    {
        $this->selectedDestino = null;
        $this->searchDestino = '';
        $this->showDestinoDropdown = false;
    }

    public function updatedSearchOrigen()
    {
        $this->showOrigenDropdown = true;
    }

    public function updatedSearchDestino()
    {
        $this->showDestinoDropdown = true;
    }

    public function updatedSearchProducto()
    {
        $this->showProductoDropdown = true;
    }

    public function seleccionarPrimerResultado()
    {
        $resultados = $this->productoResults;
        if (!empty($resultados)) {
            $primerProducto = array_values($resultados)[0];
            $this->selectProducto($primerProducto['id']);
        }
    }

    public function getProductoResultsProperty()
    {
        if (empty($this->searchProducto)) {
            return $this->productos;
        }

        $search = strtolower(trim($this->searchProducto));

        return array_filter($this->productos, function($producto) use ($search) {
            // Convertir el ID a hexadecimal para la comparación
            $idHex = strtolower(dechex($producto['id']));

            // Buscar tanto en el ID hexadecimal como en la descripción
            return str_contains(strtolower($producto['descripcion']), $search) ||
                   str_contains($idHex, str_replace(['0x', '#'], '', $search)) ||
                   str_contains((string)$producto['id'], $search);
        });
    }

    public function selectProducto($productoId)
    {
        $producto = collect($this->productos)->firstWhere('id', (int)$productoId);
        if ($producto && !collect($this->productosSeleccionados)->contains('id', $producto['id'])) {
            $this->productosSeleccionados[] = [
                'id' => $producto['id'],
                'descripcion' => $producto['descripcion'],
                'precio' => $producto['precio'],
                'cantidad' => 1
            ];
        }
        $this->searchProducto = '';
        $this->showProductoDropdown = false;
    }

    public function eliminarProducto($productoId)
    {
        $this->productosSeleccionados = array_filter($this->productosSeleccionados, function($item) use ($productoId) {
            return $item['id'] !== (int)$productoId;
        });
    }

    public function actualizarCantidad($productoId, $cantidad)
    {
        foreach ($this->productosSeleccionados as &$producto) {
            if ($producto['id'] === (int)$productoId) {
                $producto['cantidad'] = max(1, (int)$cantidad);
                break;
            }
        }
    }

    public function getSubtotalProperty()
    {
        return collect($this->productosSeleccionados)->sum(function($producto) {
            return $producto['cantidad'] * $producto['precio'];
        });
    }

    public function render()
    {
        return view('livewire.formulario-traslado');
    }
}
