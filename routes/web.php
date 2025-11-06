<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\HubPrincipal;

// Rutas de autenticación (públicas)
Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
});

// Ruta de logout (requiere autenticación)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->middleware('auth')->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    // Ruta raíz redirige al dashboard
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    // Dashboard
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // Hub Principal
    Route::get('/hub', HubPrincipal::class)->name('hub.principal');

    // Rutas de Compras
    Route::get('/compras', \App\Livewire\ComprasHub::class)->name('compras');
    Route::get('/compras/nueva', \App\Livewire\FormularioCompra::class)->name('compras.nueva');
    Route::get('/compras/historial', \App\Livewire\HistorialCompras::class)->name('compras.historial');

    // Rutas de Traslados
    Route::get('/traslados', \App\Livewire\TrasladosHub::class)->name('traslados');
    Route::get('/traslados/nuevo', \App\Livewire\FormularioTraslado::class)->name('traslados.nuevo');
    Route::get('/traslados/historial', \App\Livewire\HistorialTraslados::class)->name('traslados.historial');
    Route::get('/requisiciones', \App\Livewire\FormularioRequisicion::class)->name('requisiciones');

    // Rutas de Devoluciones
    Route::get('/devoluciones', \App\Livewire\FormularioDevolucion::class)->name('devoluciones');
    Route::get('/devoluciones/historial', \App\Livewire\HistorialDevoluciones::class)->name('devoluciones.historial');

    // Rutas de Productos
    Route::get('/productos', \App\Livewire\GestionProductos::class)->name('productos');
    Route::get('/productos/categorias', \App\Livewire\GestionCategorias::class)->name('productos.categorias');

    // Rutas de Bodegas y Responsabilidad
    Route::get('/bodegas', \App\Livewire\GestionBodegas::class)->name('bodegas');
    Route::get('/personas', \App\Livewire\GestionPersonas::class)->name('personas');
    Route::get('/tarjetas-responsabilidad', \App\Livewire\GestionTarjetasResponsabilidad::class)->name('tarjetas.responsabilidad');

    // Otras rutas
    Route::get('/usuarios', \App\Livewire\GestionUsuarios::class)->name('usuarios');
    Route::get('/proveedores', \App\Livewire\GestionProveedores::class)->name('proveedores');
    Route::get('/bitacora', \App\Livewire\BitacoraSistema::class)->name('bitacora');
    Route::get('/reportes', \App\Livewire\GenerarReportes::class)->name('reportes');
    Route::get('/configuracion', \App\Livewire\ConfiguracionSistema::class)->name('configuracion');
});
