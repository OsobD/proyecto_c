<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas de Compras
Route::get('/compras', \App\Livewire\ComprasHub::class)->name('compras');
Route::get('/compras/nueva', \App\Livewire\FormularioCompra::class)->name('compras.nueva');
Route::get('/compras/historial', \App\Livewire\HistorialCompras::class)->name('compras.historial');

// Rutas de Traslados
Route::get('/traslados', \App\Livewire\TrasladosHub::class)->name('traslados');
Route::get('/traslados/nuevo', \App\Livewire\FormularioTraslado::class)->name('traslados.nuevo');
Route::get('/traslados/historial', \App\Livewire\HistorialTraslados::class)->name('traslados.historial');
Route::get('/requisiciones', \App\Livewire\FormularioRequisicion::class)->name('requisiciones');
Route::get('/devoluciones', \App\Livewire\FormularioDevolucion::class)->name('devoluciones');

// Rutas de Productos
Route::get('/productos', \App\Livewire\GestionProductos::class)->name('productos');
Route::get('/productos/categorias', \App\Livewire\GestionCategorias::class)->name('productos.categorias');

// Otras rutas
Route::get('/bodegas', \App\Livewire\GestionBodegas::class)->name('bodegas');
Route::get('/usuarios', \App\Livewire\GestionUsuarios::class)->name('usuarios');
Route::get('/proveedores', \App\Livewire\GestionProveedores::class)->name('proveedores');
Route::get('/bitacora', \App\Livewire\BitacoraSistema::class)->name('bitacora');
Route::get('/reportes', \App\Livewire\GenerarReportes::class)->name('reportes');
Route::get('/configuracion', \App\Livewire\ConfiguracionSistema::class)->name('configuracion');
