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
        return redirect('/inicio');
    });

    // Dashboard (ahora /inicio)
    Route::get('/inicio', \App\Livewire\Dashboard::class)->name('dashboard');

    // Hub Principal
    Route::get('/hub', HubPrincipal::class)->name('hub.principal');

    // Rutas de Compras
    Route::middleware('permission:compras.acceder')->group(function () {
        Route::get('/compras', \App\Livewire\ComprasHub::class)->name('compras');
        Route::get('/compras/historial', \App\Livewire\HistorialCompras::class)->name('compras.historial');
    });
    Route::middleware('permission:compras.crear')->group(function () {
        Route::get('/compras/nueva', \App\Livewire\FormularioCompra::class)->name('compras.nueva');
    });

    // Rutas de Traslados
    Route::middleware('permission:traslados.acceder')->group(function () {
        Route::get('/traslados', \App\Livewire\TrasladosHub::class)->name('traslados');
        Route::get('/traslados/historial', \App\Livewire\HistorialTraslados::class)->name('traslados.historial');
    });
    Route::middleware('permission:traslados.crear')->group(function () {
        Route::get('/traslados/nuevo', \App\Livewire\FormularioTraslado::class)->name('traslados.nuevo');
    });

    // Requisiciones (ahora bajo /traslados/requisicion)
    Route::middleware('permission:requisiciones.acceder')->group(function () {
        // Redirect de la ruta vieja a la nueva para compatibilidad
        Route::get('/traslados/requisicion', function () {
            return redirect()->route('requisiciones.create');
        })->name('requisiciones');
        Route::get('/traslados/requisicion/nueva', \App\Livewire\FormularioRequisicion::class)->name('requisiciones.create');
        Route::get('/traslados/requisicion/{tipo}/{id}', \App\Livewire\DetalleRequisicion::class)->name('requisiciones.ver');
    });

    // Rutas de Devoluciones (ahora bajo /traslados/devolucion)
    Route::middleware('permission:devoluciones.acceder')->group(function () {
        Route::get('/traslados/devolucion/nueva', \App\Livewire\FormularioDevolucion::class)->name('devoluciones');
        Route::get('/traslados/devolucion/historial', \App\Livewire\HistorialDevoluciones::class)->name('devoluciones.historial');
    });

    // Rutas de Productos y Categorías
    Route::middleware('permission:productos.acceder')->group(function () {
        Route::get('/productos', \App\Livewire\GestionProductos::class)->name('productos');
        Route::get('/productos/lotes/{id}/ubicaciones', \App\Livewire\DetalleLote::class)->name('lotes.detalle');
    });
    Route::middleware('permission:categorias.acceder')->group(function () {
        Route::get('/productos/categorias', \App\Livewire\GestionCategorias::class)->name('productos.categorias');
    });

    // Rutas de Proveedores
    Route::middleware('permission:proveedores.acceder')->group(function () {
        Route::get('/proveedores', \App\Livewire\GestionProveedores::class)->name('proveedores');
    });

    // Rutas de Bodegas y Responsabilidad (ahora bajo /almacenes)
    Route::middleware('permission:bodegas.acceder')->group(function () {
        Route::get('/almacenes/bodegas', \App\Livewire\GestionBodegas::class)->name('bodegas');
        Route::get('/almacenes/bodegas/{id}/inventario', \App\Livewire\DetalleBodega::class)->name('bodegas.detalle');
    });
    Route::middleware('permission:tarjetas.acceder')->group(function () {
        Route::get('/almacenes/tarjetas', \App\Livewire\GestionTarjetasResponsabilidad::class)->name('tarjetas.responsabilidad');
        Route::get('/almacenes/tarjetas/{id}/activos', \App\Livewire\DetalleTarjeta::class)->name('tarjetas.detalle');
    });

    // Rutas de Colaboradores (Personas, Usuarios, Puestos)
    Route::middleware('permission:personas.acceder')->group(function () {
        Route::get('/personas', \App\Livewire\GestionPersonas::class)->name('personas');
    });
    Route::middleware('permission:usuarios.acceder')->group(function () {
        Route::get('/usuarios', \App\Livewire\GestionUsuarios::class)->name('usuarios');
    });
    Route::middleware('permission:puestos.acceder')->group(function () {
        Route::get('/almacenes/puestos', \App\Livewire\GestionPuestos::class)->name('puestos');
    });

    // Rutas de Reportes y Bitácora
    Route::middleware('permission:reportes.acceder')->group(function () {
        Route::get('/reportes', \App\Livewire\GenerarReportes::class)->name('reportes');
    });
    Route::middleware('permission:bitacora.acceder')->group(function () {
        Route::get('/bitacora', \App\Livewire\BitacoraSistema::class)->name('bitacora');
    });

    // Rutas de Aprobaciones
    Route::middleware('permission:aprobaciones.ver')->group(function () {
        Route::get('/aprobaciones', \App\Livewire\AprobacionesPendientes::class)->name('aprobaciones');
    });

    // Rutas de Configuración
    Route::middleware('permission:configuracion.acceder')->group(function () {
        Route::get('/configuracion', \App\Livewire\ConfiguracionSistema::class)->name('configuracion');
    });
    Route::middleware('permission:configuracion.roles')->group(function () {
        Route::get('/configuracion/roles', \App\Livewire\GestionRoles::class)->name('configuracion.roles');
    });
    Route::middleware('permission:configuracion.permisos')->group(function () {
        Route::get('/configuracion/permisos', \App\Livewire\GestionPermisos::class)->name('configuracion.permisos');
    });

    // Regímenes Tributarios (Acceso libre para usuarios autenticados)
    Route::get('/configuracion/regimenes', \App\Livewire\GestionRegimenes::class)->name('configuracion.regimenes');
});
