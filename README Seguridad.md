# Recomendaciones Preliminares de Seguridad para el Sistema de Inventario EEMQ

Este documento contiene una serie de recomendaciones y buenas prácticas de seguridad para tener en cuenta durante el desarrollo del backend y la futura implementación de funcionalidades en el sistema.

## 1. Validación de Datos de Entrada (Input Validation)

**Toda la información que proviene del usuario o de cualquier fuente externa debe ser validada.**

- **Nunca confíes en los datos del frontend.** Aunque el frontend tenga validaciones (por ejemplo, campos requeridos, formatos de email), estas pueden ser fácilmente eludidas. La validación real y definitiva siempre debe ocurrir en el backend (en los componentes de Livewire, controladores de Laravel, etc.).
- **Utiliza las reglas de validación de Laravel.** Para cada formulario, define reglas estrictas para cada campo. Por ejemplo:
  ```php
  $request->validate([
      'nombre' => 'required|string|max:255',
      'email' => 'required|email|unique:users',
      'cantidad' => 'required|integer|min:1',
      'fecha' => 'required|date',
  ]);
  ```
- **Sé específico.** No uses reglas genéricas si puedes ser más preciso. Si esperas un número, valida que sea un número. Si esperas una fecha, valida que sea una fecha.

## 2. Prevención de Cross-Site Scripting (XSS)

**Asegúrate de que cualquier dato que se muestre en la interfaz de usuario esté debidamente "escapado".**

- **Blade lo hace por defecto.** La sintaxis `{{ $variable }}` de Blade automáticamente escapa el contenido, previniendo que código HTML o JavaScript malicioso se ejecute en el navegador.
- **Evita ` {!! $variable !!} `.** Solo utiliza esta sintaxis si estás completamente seguro de que el contenido es seguro y no proviene de un usuario. Por ejemplo, si es contenido que un administrador ha guardado a través de un editor de texto enriquecido (WYSIWYG) que ya ha sido saneado. En este proyecto, por ahora, no hay necesidad de usarlo.

*Estado actual: La revisión del código no encontró ninguna instancia de `{!! !!}`, lo cual es excelente.*

## 3. Protección contra Cross-Site Request Forgery (CSRF)

**Protege todas las rutas que realizan acciones (POST, PUT, DELETE) contra ataques CSRF.**

- **Laravel lo maneja automáticamente.** Por defecto, el middleware `VerifyCsrfToken` de Laravel protege la aplicación.
- **Asegúrate de incluir `@csrf` en los formularios.** En los formularios de Blade que no son manejados por Livewire (si los hubiera en el futuro), asegúrate de incluir el token CSRF con la directiva `@csrf`.
- **Livewire lo maneja automáticamente.** Livewire incluye protección CSRF en sus peticiones AJAX, por lo que no necesitas hacer nada extra en los componentes.

## 4. Manejo Seguro de Subida de Archivos

Si en el futuro se implementa la subida de archivos (por ejemplo, imágenes de productos, facturas en PDF), sigue estas recomendaciones:

- **Valida el tipo de archivo y el tamaño.** No permitas que los usuarios suban cualquier tipo de archivo. Define una lista blanca de extensiones permitidas (ej. `jpg`, `png`, `pdf`) y un tamaño máximo.
  ```php
  $request->validate([
      'factura_pdf' => 'required|file|mimes:pdf|max:2048', // max 2MB
  ]);
  ```
- **No almacenes los archivos en el directorio `public` si no son de acceso público.** Almacena los archivos sensibles (como facturas) en el directorio `storage/app`, que no es accesible directamente desde el navegador. Laravel puede servir estos archivos de forma segura a través de rutas controladas.
- **No confíes en el nombre del archivo proporcionado por el usuario.** Genera un nombre de archivo único y aleatorio para evitar conflictos y posibles ataques.

## 5. Consultas a la Base de Datos (Prevención de SQL Injection)

**Utiliza siempre los mecanismos de Laravel que protegen contra la inyección de SQL.**

- **Usa Eloquent o el Query Builder de Laravel.** Estos sistemas utilizan "parameter binding" (enlace de parámetros) para construir las consultas, lo que previene que los datos de entrada del usuario sean interpretados como código SQL.
  ```php
  // BIEN (seguro)
  $users = DB::table('users')->where('email', $request->email)->first();

  // MAL (inseguro, vulnerable a SQL Injection)
  $users = DB::select("SELECT * FROM users WHERE email = '{$request->email}'");
  ```
- **Nunca uses variables directamente en consultas SQL crudas (`DB::raw`).** Si necesitas usar una consulta cruda, utiliza los enlaces de parámetros:
  ```php
  // BIEN (seguro)
  $users = DB::select('SELECT * FROM users WHERE email = ?', [$request->email]);
  ```

## 6. Control de Acceso

- **Implementa un sistema de roles y permisos.** Define claramente qué acciones puede realizar cada tipo de usuario (administrador, jefe de bodega, colaborador). El trait `TienePermisos` que se ha creado es un buen punto de partida para una simulación, pero para producción, considera usar un paquete como `spatie/laravel-permission`.
- **Autoriza cada acción en el backend.** No te fíes de que un botón esté oculto en el frontend. En cada método que realiza una acción crítica, verifica que el usuario autenticado tenga los permisos necesarios antes de ejecutar la lógica.
  ```php
  public function eliminarProducto($id)
  {
      if (!auth()->user()->can('eliminar productos')) {
          abort(403); // Prohibido
      }
      // Lógica para eliminar el producto...
  }
  ```

Estas son las recomendaciones iniciales. A medida que el proyecto crezca y se añadan nuevas funcionalidades, será importante seguir aplicando estos principios de seguridad en cada etapa del desarrollo.
