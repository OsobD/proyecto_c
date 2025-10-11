<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/bodegas', \App\Livewire\GestionBodegas::class)->name('bodegas');
Route::get('/productos', \App\Livewire\GestionProductos::class)->name('productos');
Route::get('/requisiciones', \App\Livewire\FormularioRequisicion::class)->name('requisiciones');
Route::get('/reportes', \App\Livewire\GenerarReportes::class)->name('reportes');

Route::get('/usuarios', \App\Livewire\GestionUsuarios::class)->name('usuarios');
Route::get('/proveedores', \App\Livewire\GestionProveedores::class)->name('proveedores');
Route::get('/bitacora', \App\Livewire\BitacoraSistema::class)->name('bitacora');

Route::get('/configuracion', \App\Livewire\ConfiguracionSistema::class)->name('configuracion');
Route::get('/compras/nueva', \App\Livewire\FormularioCompra::class)->name('compras.nueva');
Route::get('/devoluciones', \App\Livewire\FormularioDevolucion::class)->name('devoluciones');
