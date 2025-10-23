<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class FormularioRequisicion
 * @package App\Livewire
 * @brief Componente para gestionar el formulario de requisición de productos.
 *
 * Este componente maneja la lógica para crear una requisición de salida de
 * productos desde una bodega hacia un empleado (tarjeta). Permite seleccionar
 * origen (bodega) y destino (empleado), así como buscar y agregar productos.
 */
class FormularioRequisicion extends Component
{
    // --- PROPIEDADES PÚBLICAS ---

    /** @var array Datos de ejemplo para empleados, bodegas y productos. */
    public $empleados = [];
    public $bodegas = [];
    public $productos = [];

    /** @var string Término de búsqueda para la bodega de origen. */
    public $searchOrigen = '';
    /** @var string Término de búsqueda para el empleado de destino. */
    public $searchDestino = '';
    /** @var string Término de búsqueda para los productos. */
    public $searchProducto = '';

    /** @var array|null Bodega de origen seleccionada. */
    public $selectedOrigen = null;
    /** @var array|null Empleado de destino seleccionado. */
    public $selectedDestino = null;

    /** @var bool Controla la visibilidad del dropdown de resultados para el origen. */
    public $showOrigenDropdown = false;
    /** @var bool Controla la visibilidad del dropdown de resultados para el destino. */
    public $showDestinoDropdown = false;
    /** @var bool Controla la visibilidad del dropdown de resultados para los productos. */
    public $showProductoDropdown = false;

    /** @var array Lista de productos agregados a la requisición. */
    public $productosSeleccionados = [];

    // --- MÉTODOS DE CICLO DE VIDA ---

    /**
     * @brief Método que se ejecuta al inicializar el componente.
     * Carga datos de ejemplo para bodegas, empleados y productos.
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

    /**
     * @brief Hook que se ejecuta al actualizar propiedades de búsqueda.
     * Muestra el dropdown de resultados correspondiente.
     * @return void
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['searchOrigen', 'searchDestino', 'searchProducto'])) {
            $dropdown = 'show' . ucfirst(str_replace('search', '', $propertyName)) . 'Dropdown';
            $this->$dropdown = true;
        }
    }

    // --- PROPIEDADES COMPUTADAS ---

    /**
     * @brief Filtra las bodegas para el campo "origen" según el término de búsqueda.
     * @return array
     */
    public function getOrigenResultsProperty()
    {
        $search = strtolower($this->searchOrigen);
        return collect($this->bodegas)
            ->filter(fn($bodega) => empty($search) || str_contains(strtolower($bodega['nombre']), $search))
            ->map(fn($bodega) => ['id' => 'B' . $bodega['id'], 'nombre' => $bodega['nombre'], 'tipo' => 'Bodega'])
            ->all();
    }

    /**
     * @brief Filtra los empleados para el campo "destino" según el término de búsqueda.
     * @return array
     */
    public function getDestinoResultsProperty()
    {
        $search = strtolower($this->searchDestino);
        return collect($this->empleados)
            ->filter(fn($empleado) => empty($search) || str_contains(strtolower($empleado['nombre']), $search))
            ->map(fn($empleado) => ['id' => 'E' . $empleado['id'], 'nombre' => $empleado['nombre'], 'tipo' => 'Tarjeta'])
            ->all();
    }

    /**
     * @brief Filtra los productos basados en el término de búsqueda.
     * @return array
     */
    public function getProductoResultsProperty()
    {
        if (empty($this->searchProducto)) return $this->productos;
        $search = strtolower(trim($this->searchProducto));
        return array_filter($this->productos, fn($p) =>
            str_contains(strtolower($p['descripcion']), $search) ||
            str_contains(strtolower(dechex($p['id'])), $search)
        );
    }

    /**
     * @brief Calcula el subtotal del valor de los productos en la requisición.
     * @return float
     */
    public function getSubtotalProperty()
    {
        return collect($this->productosSeleccionados)->sum(fn($p) => $p['cantidad'] * $p['precio']);
    }

    // --- MÉTODOS DE MANEJO DE SELECCIÓN ---

    /**
     * @brief Establece la bodega de origen seleccionada.
     * @param string $id ID de la bodega.
     * @param string $nombre Nombre de la bodega.
     * @param string $tipo Tipo ('Bodega').
     * @return void
     */
    public function selectOrigen($id, $nombre, $tipo)
    {
        $this->selectedOrigen = ['id' => $id, 'nombre' => $nombre, 'tipo' => $tipo];
        $this->searchOrigen = '';
        $this->showOrigenDropdown = false;
    }

    /**
     * @brief Establece el empleado de destino seleccionado.
     * @param string $id ID del empleado.
     * @param string $nombre Nombre del empleado.
     * @param string $tipo Tipo ('Tarjeta').
     * @return void
     */
    public function selectDestino($id, $nombre, $tipo)
    {
        $this->selectedDestino = ['id' => $id, 'nombre' => $nombre, 'tipo' => $tipo];
        $this->searchDestino = '';
        $this->showDestinoDropdown = false;
    }

    /**
     * @brief Limpia la selección del origen.
     * @return void
     */
    public function clearOrigen()
    {
        $this->selectedOrigen = null;
    }

    /**
     * @brief Limpia la selección del destino.
     * @return void
     */
    public function clearDestino()
    {
        $this->selectedDestino = null;
    }

    /**
     * @brief Selecciona el primer producto de la lista de resultados de búsqueda.
     * @return void
     */
    public function seleccionarPrimerResultado()
    {
        $resultados = $this->productoResults;
        if (!empty($resultados)) {
            $this->selectProducto(array_values($resultados)[0]['id']);
        }
    }

    /**
     * @brief Selecciona un producto y lo agrega a la lista de requisición.
     * @param int $productoId ID del producto.
     * @return void
     */
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

    /**
     * @brief Elimina un producto de la lista de requisición.
     * @param int $productoId ID del producto a eliminar.
     * @return void
     */
    public function eliminarProducto($productoId)
    {
        $this->productosSeleccionados = array_values(array_filter(
            $this->productosSeleccionados,
            fn($item) => $item['id'] !== (int)$productoId
        ));
    }

    /**
     * @brief Actualiza la cantidad de un producto en la lista.
     * @param int $productoId ID del producto a actualizar.
     * @param int $cantidad Nueva cantidad (mínimo 1).
     * @return void
     */
    public function actualizarCantidad($productoId, $cantidad)
    {
        foreach ($this->productosSeleccionados as &$producto) {
            if ($producto['id'] === (int)$productoId) {
                $producto['cantidad'] = max(1, (int)$cantidad);
                break;
            }
        }
    }

    /**
     * @brief Renderiza la vista del componente.
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.formulario-requisicion');
    }
}
