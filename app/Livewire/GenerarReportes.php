<?php

namespace App\Livewire;

use Livewire\Component;

class GenerarReportes extends Component
{
    public $tabActivo = 'compras';
    public $tipoReporte = '';
    public $fechaInicio = '';
    public $fechaFin = '';
    public $usuarioSeleccionado = '';
    public $proveedorSeleccionado = '';
    public $bodegaSeleccionada = '';

    public $usuarios = [];
    public $proveedores = [];
    public $bodegas = [];

    // Reportes por categoría
    public $reportesCompras = [];
    public $reportesTraslados = [];
    public $reportesInventario = [];
    public $reportesBitacora = [];

    public function mount()
    {
        // Datos simulados
        $this->usuarios = [
            ['id' => 1, 'nombre' => 'Juan Pérez'],
            ['id' => 2, 'nombre' => 'María García'],
            ['id' => 3, 'nombre' => 'Carlos López'],
            ['id' => 4, 'nombre' => 'David Bautista'],
        ];

        $this->proveedores = [
            ['id' => 1, 'nombre' => 'Ferretería El Martillo Feliz'],
            ['id' => 2, 'nombre' => 'Suministros Industriales S.A.'],
            ['id' => 3, 'nombre' => 'Distribuidora García'],
        ];

        $this->bodegas = [
            ['id' => 1, 'nombre' => 'Bodega Central'],
            ['id' => 2, 'nombre' => 'Bodega Norte'],
            ['id' => 3, 'nombre' => 'Bodega Sur'],
        ];

        $this->reportesCompras = [
            ['id' => 'compras_proveedor', 'nombre' => 'Compras por Proveedor'],
            ['id' => 'compras_periodo', 'nombre' => 'Compras por Período'],
            ['id' => 'analisis_costos', 'nombre' => 'Análisis de Costos'],
            ['id' => 'compras_categoria', 'nombre' => 'Compras por Categoría'],
        ];

        $this->reportesTraslados = [
            ['id' => 'traslados_bodega', 'nombre' => 'Traslados por Bodega'],
            ['id' => 'traslados_periodo', 'nombre' => 'Traslados por Período'],
            ['id' => 'requisiciones_area', 'nombre' => 'Requisiciones por Área'],
            ['id' => 'devoluciones', 'nombre' => 'Reporte de Devoluciones'],
        ];

        $this->reportesInventario = [
            ['id' => 'inventario_bodega', 'nombre' => 'Inventario por Bodega'],
            ['id' => 'tarjeta_responsabilidad', 'nombre' => 'Tarjeta de Responsabilidad'],
            ['id' => 'movimientos_producto', 'nombre' => 'Movimientos de Producto'],
            ['id' => 'stock_minimo', 'nombre' => 'Productos con Stock Mínimo'],
        ];

        $this->reportesBitacora = [
            ['id' => 'actividad_usuario', 'nombre' => 'Actividad por Usuario'],
            ['id' => 'actividad_periodo', 'nombre' => 'Actividad por Período'],
            ['id' => 'cambios_inventario', 'nombre' => 'Cambios en Inventario'],
            ['id' => 'log_sistema', 'nombre' => 'Log del Sistema'],
        ];
    }

    public function cambiarTab($tab)
    {
        $this->tabActivo = $tab;
        $this->tipoReporte = '';
    }

    public function generarReporte()
    {
        if (empty($this->tipoReporte)) {
            session()->flash('error', 'Debe seleccionar un tipo de reporte.');
            return;
        }

        session()->flash('message', 'Generando reporte: ' . $this->tipoReporte);
    }

    public function exportarExcel()
    {
        session()->flash('message', 'Exportando a Excel...');
    }

    public function imprimir()
    {
        session()->flash('message', 'Preparando impresión...');
    }

    public function render()
    {
        return view('livewire.generar-reportes');
    }
}
