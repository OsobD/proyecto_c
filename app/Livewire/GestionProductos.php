<?php

namespace App\Livewire;

use Livewire\Component;

class GestionProductos extends Component
{
    public $productos = [];
    public $categorias = [];
    public $searchProducto = '';
    public $showModal = false;
    public $showSubModalCategoria = false;
    public $showHistorial = null;
    public $editingId = null;

    // Campos del formulario de producto
    public $codigo = '';
    public $descripcion = '';
    public $categoriaId = '';

    // Campo para crear categoría
    public $nuevaCategoriaNombre = '';

    public function mount()
    {
        $this->categorias = [
            ['id' => 1, 'nombre' => 'Herramientas', 'activo' => true],
            ['id' => 2, 'nombre' => 'Materiales Eléctricos', 'activo' => true],
            ['id' => 3, 'nombre' => 'Equipos de Seguridad', 'activo' => true],
            ['id' => 4, 'nombre' => 'Suministros de Oficina', 'activo' => true],
        ];

        $this->productos = [
            [
                'id' => 1,
                'codigo' => 'PROD-001',
                'descripcion' => 'Tornillos de acero inoxidable',
                'categoria_id' => 1,
                'activo' => true,
                'historial' => [
                    ['fecha' => '2024-01-15', 'proveedor' => 'Ferretería El Martillo', 'costo' => 15.50, 'factura' => 'F-001'],
                    ['fecha' => '2024-02-20', 'proveedor' => 'Ferretería El Martillo', 'costo' => 16.00, 'factura' => 'F-045'],
                ],
            ],
            [
                'id' => 2,
                'codigo' => 'PROD-002',
                'descripcion' => 'Abrazaderas de metal',
                'categoria_id' => 2,
                'activo' => true,
                'historial' => [
                    ['fecha' => '2024-01-10', 'proveedor' => 'Suministros Industriales', 'costo' => 5.75, 'factura' => 'F-120'],
                ],
            ],
            [
                'id' => 3,
                'codigo' => 'PROD-003',
                'descripcion' => 'Cinta aislante',
                'categoria_id' => 2,
                'activo' => true,
                'historial' => [],
            ],
            [
                'id' => 4,
                'codigo' => 'PROD-004',
                'descripcion' => 'Guantes de seguridad',
                'categoria_id' => 3,
                'activo' => true,
                'historial' => [],
            ],
            [
                'id' => 5,
                'codigo' => 'PROD-005',
                'descripcion' => 'Fusibles de 15A',
                'categoria_id' => 2,
                'activo' => false,
                'historial' => [],
            ],
        ];
    }

    public function getProductosFiltradosProperty()
    {
        if (empty($this->searchProducto)) {
            return $this->productos;
        }

        $search = strtolower(trim($this->searchProducto));

        return array_filter($this->productos, function($producto) use ($search) {
            $categoriaNombre = $this->getNombreCategoria($producto['categoria_id']);
            return str_contains(strtolower($producto['codigo']), $search) ||
                   str_contains(strtolower($producto['descripcion']), $search) ||
                   str_contains(strtolower($categoriaNombre), $search);
        });
    }

    public function getCategoriasActivasProperty()
    {
        return array_filter($this->categorias, fn($cat) => $cat['activo']);
    }

    public function getNombreCategoria($categoriaId)
    {
        $categoria = collect($this->categorias)->firstWhere('id', $categoriaId);
        return $categoria ? $categoria['nombre'] : 'Sin categoría';
    }

    public function abrirModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editarProducto($id)
    {
        $producto = collect($this->productos)->firstWhere('id', $id);

        if ($producto) {
            $this->editingId = $id;
            $this->codigo = $producto['codigo'];
            $this->descripcion = $producto['descripcion'];
            $this->categoriaId = $producto['categoria_id'];
            $this->showModal = true;
        }
    }

    public function guardarProducto()
    {
        $this->validate([
            'codigo' => 'required|min:3|max:50',
            'descripcion' => 'required|min:3|max:255',
            'categoriaId' => 'required',
        ], [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.min' => 'El código debe tener al menos 3 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 3 caracteres.',
            'categoriaId.required' => 'Debe seleccionar una categoría.',
        ]);

        if ($this->editingId) {
            // Actualizar producto existente
            $this->productos = array_map(function($prod) {
                if ($prod['id'] === $this->editingId) {
                    $prod['codigo'] = $this->codigo;
                    $prod['descripcion'] = $this->descripcion;
                    $prod['categoria_id'] = (int)$this->categoriaId;
                }
                return $prod;
            }, $this->productos);
        } else {
            // Crear nuevo producto
            $newId = max(array_column($this->productos, 'id')) + 1;
            $this->productos[] = [
                'id' => $newId,
                'codigo' => $this->codigo,
                'descripcion' => $this->descripcion,
                'categoria_id' => (int)$this->categoriaId,
                'activo' => true,
                'historial' => [],
            ];
        }

        $this->closeModal();
        session()->flash('message', $this->editingId ? 'Producto actualizado exitosamente.' : 'Producto creado exitosamente.');
    }

    public function toggleEstado($id)
    {
        $this->productos = array_map(function($prod) use ($id) {
            if ($prod['id'] === $id) {
                $prod['activo'] = !$prod['activo'];
            }
            return $prod;
        }, $this->productos);

        session()->flash('message', 'Estado del producto actualizado.');
    }

    public function toggleHistorial($id)
    {
        $this->showHistorial = $this->showHistorial === $id ? null : $id;
    }

    // Sub-modal de categorías
    public function abrirSubModalCategoria()
    {
        $this->nuevaCategoriaNombre = '';
        $this->showSubModalCategoria = true;
    }

    public function guardarNuevaCategoria()
    {
        $this->validate([
            'nuevaCategoriaNombre' => 'required|min:3|max:100',
        ], [
            'nuevaCategoriaNombre.required' => 'El nombre de la categoría es obligatorio.',
            'nuevaCategoriaNombre.min' => 'El nombre debe tener al menos 3 caracteres.',
        ]);

        $newId = max(array_column($this->categorias, 'id')) + 1;
        $this->categorias[] = [
            'id' => $newId,
            'nombre' => $this->nuevaCategoriaNombre,
            'activo' => true,
        ];

        $this->categoriaId = $newId;
        $this->showSubModalCategoria = false;
        $this->nuevaCategoriaNombre = '';
    }

    public function closeSubModalCategoria()
    {
        $this->showSubModalCategoria = false;
        $this->nuevaCategoriaNombre = '';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->codigo = '';
        $this->descripcion = '';
        $this->categoriaId = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.gestion-productos');
    }
}
