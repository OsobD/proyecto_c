## Instalación inicial
```bash
git clone <url>
cd eemq_preliminar
git checkout <rama>
composer install
npm install              # NO instalará Alpine - viene incluido en Livewire
cp .env.docker .env
docker-compose build --no-cache
docker-compose up -d

# Esperar 20 segundos a que la DB esté lista, luego ejecutar migraciones
docker-compose exec app php artisan migrate:fresh --seed
npm run dev              # Mantener corriendo durante desarrollo

```

**Credenciales de Acceso preventiva:**
- Usuario: `admin`
- Contraseña: `admin123`


## Sistema de Autenticación

El sistema cuenta con **autenticación completa** implementada con Livewire y Argon2id:

### Características:
- ✅ **Login** - Pantalla de inicio de sesión con validación
- ✅ **Register** - Registro de nuevos usuarios (rol Operador por defecto)
- ✅ **Dashboard** - Panel principal con estadísticas del sistema
- ✅ **Logout** - Cierre de sesión seguro
- ✅ **Middleware Auth** - Todas las rutas protegidas automáticamente
- ✅ **Guest Middleware** - Login/Register solo accesibles sin autenticación

### Rutas de Autenticación:
- `/login` - Iniciar sesión
- `/register` - Crear cuenta nueva
- `/dashboard` - Panel principal (requiere autenticación)
- `/` - Redirige a `/dashboard` si está autenticado, o `/login` si no lo está

### Protección de Rutas:
**Todas las rutas del sistema están protegidas**:
- Si intentas acceder sin login → redirige a `/login`
- Las rutas de login/register solo son accesibles si NO estás autenticado

**⚠️ IMPORTANTE - Alpine.js:**
- Alpine.js NO está en `package.json` porque Livewire 3 lo incluye automáticamente
- NO agregues `alpinejs` como dependencia npm
- NO importes Alpine en `resources/js/bootstrap.js`
- Si necesitas plugins de Alpine, consulta la sección Troubleshooting

**Acceder a la aplicación:**
- Frontend: http://localhost:8000
- Base de datos MySQL: localhost:3306 (usuario: eemq_user, password: secret)

## Comandos diarios
```bash
docker-compose up -d    # Levantar contenedores
npm run dev             # Iniciar Vite (mantener corriendo)
```

## Para producción
```bash
npm run build           # Solo para despliegue
```

## Troubleshooting

### ❌ Modales y dropdowns no funcionan (Alpine.js)

**Síntomas:**
- Botones para abrir modales no funcionan
- Dropdowns con `x-data` no responden
- `@entangle()` no sincroniza datos entre Livewire y Alpine
- Console del navegador no muestra errores de Alpine

**Causa:**
Livewire 3 **ya incluye Alpine.js** automáticamente. Importar Alpine manualmente desde npm causa conflictos porque:
1. Livewire espera controlar cuándo Alpine se inicia
2. Dos versiones de Alpine pueden cargarse (la de npm y la de Livewire)
3. `@entangle()` requiere que Livewire inicie Alpine en el momento correcto

**Solución:**
```bash
# 1. Eliminar importación de Alpine en resources/js/bootstrap.js
# ANTES:
import Alpine from 'alpinejs';
window.Alpine = Alpine;

# DESPUÉS:
// Alpine.js viene incluido en Livewire 3 - no es necesario importarlo

# 2. Eliminar dependencia de package.json
# Quitar la sección "dependencies": { "alpinejs": "..." }

# 3. Reinstalar dependencias
npm install

# 4. Reiniciar Vite
npm run dev

# 5. Recargar navegador (Ctrl + Shift + R)
```

**Regla importante:**
- ✅ **Usar Alpine incluido en Livewire** (recomendado)
- ❌ **NO importar Alpine manualmente** a menos que necesites plugins específicos
- ❌ **NUNCA llamar `Alpine.start()` manualmente** - Livewire lo hace automáticamente
- Si necesitas plugins de Alpine, usa el evento `livewire:init` para configurarlos

**Nota histórica:**
El problema original era que se llamaba `Alpine.start()` antes de que Livewire se cargara, rompiendo `@entangle()`. Esto se resolvió eliminando `Alpine.start()` y dejando que Livewire inicie Alpine automáticamente. Posteriormente, se descubrió que incluso importar Alpine causaba conflictos, por lo que se eliminó completamente la dependencia de npm.

### Dropdowns no funcionan / Vite no está corriendo
```bash
# 1. Verificar Vite
netstat -ano | findstr :5173       # Debe mostrar LISTENING
cat public/hot                     # Debe mostrar http://localhost:5173

# 2. Si Vite no está corriendo, limpiar y reiniciar
Get-Process -Name node | Stop-Process -Force  # PowerShell
rm -f public/hot
rm -rf public/build
rm -rf node_modules/.vite
npm cache clean --force
npm run dev

# 3. Recargar navegador
# Ctrl + Shift + R (Windows/Linux)
# Cmd + Shift + R (Mac)
```

## Layouts

**Componentes Livewire usan:** `resources/views/components/layouts/app.blade.php`

**Orden correcto de scripts:**
```blade
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
```

**No cargar `@vite()` dos veces** - Rompe Alpine.js