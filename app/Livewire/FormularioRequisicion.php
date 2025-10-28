<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Componente FormularioRequisicion
 *
 * Formulario para registrar requisiciones de productos desde bodega hacia
 * tarjetas de responsabilidad (empleados).
 * Flujo: Bodega → Tarjeta de Responsabilidad (Empleado)
 *
 * @package App\Livewire
 * @see resources/views/livewire/formulario-requisicion.blade.php
 */
class FormularioRequisicion extends Component
{
    /** @var array Listado de empleados (tarjetas de responsabilidad) */
    public $empleados = [];

    /** @var array Listado de bodegas */
    public $bodegas = [];

    /** @var array Listado de productos */
    public $productos = [];

    /** @var string Término de búsqueda para bodega origen */
    public $searchOrigen = '';

    /** @var string Término de búsqueda para empleado destino */
    public $searchDestino = '';

    /** @var string Término de búsqueda de producto */
    public $searchProducto = '';

    /** @var array|null Bodega origen seleccionada */
    public $selectedOrigen = null;

    /** @var array|null Empleado destino seleccionado */
    public $selectedDestino = null;

    /** @var bool Controla dropdown de bodega origen */
    public $showOrigenDropdown = false;

    /** @var bool Controla dropdown de empleado destino */
    public $showDestinoDropdown = false;

    /** @var bool Controla dropdown de productos */
    public $showProductoDropdown = false;

    /** @var array Productos agregados a la requisición */
    public $productosSeleccionados = [];

    /**
     * Inicializa el componente con datos mock de prueba
     *
     * @todo Reemplazar con consultas a BD: Bodega::all(), User::all(), Producto::all()
     * @return void
     */
    public function mount()
    {
        $this->bodegas = [
            ['id' => 1, 'nombre' => 'Bodega Central'],
            ['id' => 2, 'nombre' => 'Bodega Norte'],
            ['id' => 3, 'nombre' => 'Bodega Sur'],
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

    public function getOrigenResultsProperty()
    {
        $results = [];

        // Only show bodegas as origin (Bodega -> Tarjeta)
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

        // Only show empleados/tarjetas as destination (Bodega -> Tarjeta)
        foreach ($this->empleados as $empleado) {
            if (empty($this->searchDestino) ||
                str_contains(strtolower($empleado['nombre']), strtolower($this->searchDestino))) {
                $results[] = [
                    'id' => 'E' . $empleado['id'],
                    'nombre' => $empleado['nombre'],
                    'tipo' => 'Tarjeta'
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

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.formulario-requisicion');
    }
}
