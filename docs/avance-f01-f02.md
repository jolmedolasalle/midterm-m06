# Listado y ficha de empleados: F01 y F02

Este bloque utiliza los doce empleados de la plantilla original. No necesita base de datos ni paquetes nuevos.

## Cómo se atiende una petición

1. La ruta identifica la acción que debe ejecutar `EmployeeController`.
2. El controlador obtiene los datos de `Staff` y los prepara.
3. La vista Blade recibe esos datos y utiliza el layout común para mostrar la página.

## F01: listado, filtro y ordenación

El formulario envía una petición GET. `role` elige el rol y `sort` permite ordenar por nombre o por horas ascendentes. El controlador filtra antes de ordenar. Cuando dos empleados tienen las mismas horas, desempata por nombre.

Para comparar nombres, `Str::ascii()` elimina las tildes de la comparación y `strcasecmp()` ignora las mayúsculas. El nombre que ve el usuario conserva su escritura original.

Un rol desconocido se trata como «Todos los roles». Una ordenación desconocida utiliza el nombre. Los valores con forma de array también vuelven a estos valores predeterminados, evitando errores de tipo.

## F02: búsqueda y error 404

`Staff::findOrFail()` utiliza la búsqueda existente y lanza `EmployeeNotFoundException` si no encuentra al empleado. La excepción solo comunica un problema con los datos: no devuelve respuestas, vistas ni códigos HTTP.

`bootstrap/app.php` convierte esa excepción en una respuesta con código 404 y la vista propia `errors/404.blade.php`. La página no muestra la traza. El método original `Staff::find()` mantiene su comportamiento: devuelve `null` cuando no encuentra al empleado, porque otros componentes de la plantilla pueden necesitarlo.

La ruta de la ficha usa `whereNumber('id')`. Por tanto, una dirección como `/empleados/abc` no llega al controlador. Un identificador numérico que no existe también termina en un 404.

## Vistas

Las páginas heredan de `layout.blade.php`. Los enlaces y acciones del formulario se generan con `route()`, y las hojas de estilo con `asset()`. Los datos se imprimen con `{{ }}` para escapar el HTML.

## Comprobación en el entorno del curso

Desde la carpeta del proyecto, con Docker Desktop iniciado:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan test --filter='F01_EmployeeListTest|F02_EmployeeSheetTest|EmployeePagesEdgeCasesTest'
```

Comprobar también en el navegador el listado, el filtro por almacén, la ordenación por horas, una ficha existente y el identificador 999. Las pruebas del profesor se mantienen intactas; las adicionales cubren nombres con tildes y entradas mal formadas.

Este bloque todavía no implementa el cuadrante ni su redirección desde la raíz. Esas funciones se comprobarán con sus pruebas al completar los siguientes apartados.
