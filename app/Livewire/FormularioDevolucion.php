<?php

namespace App\Livewire;

use Livewire\Component;

class FormularioDevolucion extends Component
{
    public $empleados = [];
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
    public $motivo = '';

    public function mount()
    {
        $this->bodegas = [
            ['id' => 1, 'nombre' => 'Bodega Central'],
            ['id' => 2, 'nombre' => 'Bodega Norte'],
            ['id' => 3, 'nombre' => 'Bodega Sur'],
            ['id' => 4, 'nombre' => 'Bodega de Devoluciones'],
        ];

        $this->empleados = [
            ['id' => 1, 'nombre' => 'Juan Pérez'],
            ['id' => 2, 'nombre' => 'María García'],
            ['id' => 3, 'nombre' => 'Carlos López'],
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

    private function normalizeString($string)
    {
        // Normalizar para búsqueda insensible a tildes y mayúsculas
        $string = mb_strtolower($string, 'UTF-8');
        $unwanted = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'ñ' => 'n', 'Ñ' => 'n'
        ];
        return strtr($string, $unwanted);
    }

    public function getOrigenResultsProperty()
    {
        $results = [];
        $search = trim($this->searchOrigen);

        // Always add bodegas first
        foreach ($this->bodegas as $bodega) {
            if (empty($search) ||
                str_contains($this->normalizeString($bodega['nombre']), $this->normalizeString($search))) {
                $results[] = [
                    'id' => 'B' . $bodega['id'],
                    'nombre' => $bodega['nombre'],
                    'tipo' => 'Bodega'
                ];
            }
        }

        // Then add empleados
        foreach ($this->empleados as $empleado) {
            if (empty($search) ||
                str_contains($this->normalizeString($empleado['nombre']), $this->normalizeString($search))) {
                $results[] = [
                    'id' => 'E' . $empleado['id'],
                    'nombre' => $empleado['nombre'],
                    'tipo' => 'Empleado'
                ];
            }
        }

        return $results;
    }

    public function getDestinoResultsProperty()
    {
        $results = [];
        $search = trim($this->searchDestino);

        // Always add bodegas first
        foreach ($this->bodegas as $bodega) {
            if (empty($search) ||
                str_contains($this->normalizeString($bodega['nombre']), $this->normalizeString($search))) {
                $results[] = [
                    'id' => 'B' . $bodega['id'],
                    'nombre' => $bodega['nombre'],
                    'tipo' => 'Bodega'
                ];
            }
        }

        // Then add empleados
        foreach ($this->empleados as $empleado) {
            if (empty($search) ||
                str_contains($this->normalizeString($empleado['nombre']), $this->normalizeString($search))) {
                $results[] = [
                    'id' => 'E' . $empleado['id'],
                    'nombre' => $empleado['nombre'],
                    'tipo' => 'Empleado'
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
        if (empty(trim($this->searchProducto))) {
            return [];
        }

        $search = trim($this->searchProducto);
        $searchNormalized = $this->normalizeString($search);
        $searchClean = str_replace(['0x', '#', ' '], '', $searchNormalized);

        return array_filter($this->productos, function($producto) use ($searchNormalized, $searchClean) {
            // Convertir el ID a hexadecimal para la comparación
            $idHex = strtolower(dechex($producto['id']));

            // Normalizar la descripción
            $descripcionNormalized = $this->normalizeString($producto['descripcion']);

            // Buscar en descripción, ID hexadecimal, o ID decimal
            return str_contains($descripcionNormalized, $searchNormalized) ||
                   str_contains($idHex, $searchClean) ||
                   str_contains((string)$producto['id'], $searchClean);
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
        return view('livewire.formulario-devolucion');
    }
}
