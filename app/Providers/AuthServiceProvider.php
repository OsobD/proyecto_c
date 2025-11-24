<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Usuario;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir Gate dinámico para verificar permisos
        Gate::before(function (Usuario $user, $ability) {
            // El ability será el nombre del permiso (ej: 'compras.crear')
            if ($user->tienePermiso($ability)) {
                return true;
            }

            // Si no tiene el permiso específico, no permitir
            return null;
        });

        // Gates específicos para roles
        Gate::define('es-administrador', function (Usuario $user) {
            return $user->esAdministrador();
        });

        Gate::define('es-jefe-bodega', function (Usuario $user) {
            return $user->esJefeBodega();
        });

        Gate::define('es-colaborador-bodega', function (Usuario $user) {
            return $user->esColaboradorBodega();
        });

        Gate::define('es-colaborador-contabilidad', function (Usuario $user) {
            return $user->esColaboradorContabilidad();
        });

        // Gate para verificar si puede aprobar
        Gate::define('puede-aprobar', function (Usuario $user) {
            return $user->esAdministrador() || $user->esJefeBodega();
        });
    }
}
