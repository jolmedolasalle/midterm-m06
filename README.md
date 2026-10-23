# Cuadrante de turnos · Midterm M06

Aplicación para organizar el cuadrante semanal de un comercio con doce empleados y tres turnos diarios.

PHP 8.4 y Laravel 13 con Docker y Laravel Sail. Los empleados y horarios son datos fijos; el cuadrante y su título se guardan en la sesión. No utiliza base de datos, registro de usuarios ni compilación de frontend.

## Funcionalidades

| Apartado | Implementación |
| --- | --- |
| F01 | Listado de empleados con filtro por rol y ordenación por nombre u horas. |
| F02 | Ficha con id numérico, excepción propia y página 404 sin traza. |
| F03 | Cuadrante semanal: asignar, sustituir y quitar, con redirección y mensaje flash. |
| F04 | Form Request y reglas propias para un turno por empleado y día, y descanso mínimo de 12 horas. |
| F05 | Resumen de horas, cobertura y días sin responsable; los avisos no bloquean. |
| F06 | Contrato `RosterStore`, implementaciones intercambiables y selección en el provider. |
| F07 | PHPDoc del almacenamiento en sesión y de su comportamiento inicial. |
| F08, optativa | Consulta de empleados que pueden cubrir una celda vacía. |
| F09, optativa | Asignación del primer candidato válido, recalculado al enviar el formulario. |

Se eligen exactamente F08 y F09 como optativas. El orden de candidatos es el de `Staff::all()`. Los avisos de exceso de contrato no excluyen personas: solo bloquean R01 y R02.

## Requisitos

Docker y Docker Compose. El entorno Sail del repositorio utiliza PHP 8.4 y la aplicación utiliza Laravel 13.

En Windows, ejecutar los comandos dentro de Ubuntu en WSL2, con Docker Desktop abierto y la integración WSL activada. En Linux o macOS, utilizar la terminal correspondiente. No ejecutar Sail desde PowerShell.

### Primera instalación en una copia nueva

Desde la raíz del proyecto, instalar las dependencias con el contenedor de Composer de Sail:

```bash
docker run --rm -e COMPOSER_PROCESS_TIMEOUT=0 -v "$(pwd):/var/www/html" -w /var/www/html \
  laravelsail/php84-composer:latest composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

La copia de `.env` y la generación de la clave se hacen una vez en una instalación nueva. `.env` contiene la configuración local y no se sube a Git. No hay migraciones que ejecutar.

### Trabajo habitual

```bash
./vendor/bin/sail up -d
```

Abrir <http://localhost> (o el puerto configurado en `APP_PORT`): la raíz redirige al cuadrante. Desde su navegación se accede a empleados y resumen.

Para detener los contenedores:

```bash
./vendor/bin/sail down
```

## Pruebas

```bash
./vendor/bin/sail artisan test
```

Las pruebas de `tests/Feature` cubren empleados, validaciones, contrato, persistencia en sesión, operaciones del cuadrante, resumen y candidatos. El workflow `.github/workflows/tests.yml` ejecuta `php artisan test` con PHP 8.4.

## Contrato y sesión

`app/Contracts/RosterStore.php` define seis operaciones: `all()`, `assign()`, `remove()`, `forEmployee()`, `title()` y `setTitle()`. El cuadrante es un mapa de día → turno → id de empleado; una celda vacía contiene `null`. El almacén no valida reglas de negocio: los Form Requests y las reglas comprueban las entradas antes de guardar.

Los controladores reciben `RosterStore` por inyección de dependencias. `app/Providers/AppServiceProvider.php` enlaza el contrato con `SessionRosterStore`. Para utilizar `ArrayRosterStore`, basta con cambiar la clase enlazada en ese provider, sin modificar controladores ni vistas. Esta implementación mantiene un ejemplo en memoria y sus cambios se pierden al terminar la petición.

`SessionRosterStore` utiliza las claves `roster.week` y `roster.title`. Una sesión nueva devuelve siete días con todas sus celdas vacías y un título vacío. Los cambios persisten entre peticiones de la misma sesión. Con `SESSION_DRIVER=file`, configurado en `.env.example`, Laravel guarda la sesión en `storage/framework/sessions`.

El resumen se recalcula al consultarlo. El título se muestra escapado en Blade y los mensajes flash se presentan en la siguiente petición.
