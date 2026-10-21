# Cuadrante de turnos · Midterm M06

Aplicación de Acceso a Datos para organizar la semana de un comercio con doce empleados y tres turnos diarios. Parte del proyecto base y de la plantilla Classroom del curso.

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

## Entorno

En Windows, ejecutar los comandos dentro de Ubuntu en WSL2, con Docker Desktop abierto y la integración WSL activada. En Linux o macOS, utilizar la terminal correspondiente. No ejecutar Sail desde PowerShell.

### Primera instalación en una copia nueva

Desde la raíz del proyecto, instalar las dependencias con el contenedor de Composer del curso:

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

Abrir <http://localhost>: la raíz redirige al cuadrante. Desde su navegación se accede a empleados y resumen.

Para detener los contenedores:

```bash
./vendor/bin/sail down
```

## Pruebas

```bash
./vendor/bin/sail artisan test
```

Las pruebas suministradas en Classroom se conservan. Las adicionales comprueban casos límite de filtros, identificadores, reglas de descanso, sesión, eliminación, título escapado, avisos y optativas. La prueba de portada de `SmokeTest`, procedente de la base, se adapta a la redirección exigida en el enunciado.

El flujo `.github/workflows/tests.yml`, proporcionado por el curso, ejecuta las pruebas con PHP 8.4 en GitHub Actions. Para trabajar localmente se utiliza Sail.

## Contrato y sesión

`RosterStore` conserva sus seis métodos originales. Los controladores que utilizan el cuadrante reciben esa interfaz en el constructor.

En `app/Providers/AppServiceProvider.php`, la implementación de entrega es:

```php
\App\Stores\SessionRosterStore::class,
```

Para la demostración F06, cambiar solo esa línea por `\App\Stores\ArrayRosterStore::class,`. La aplicación mostrará el ejemplo fijo y las asignaciones no persistirán entre peticiones. Después de la demostración, volver a `SessionRosterStore`.

Las claves de sesión son `roster.week` y `roster.title`. Una sesión nueva devuelve siete días con todas sus celdas vacías y un título vacío. El resumen se recalcula al consultarlo.

## Organización y explicación

- [Guía para entender y explicar el proyecto](docs/guia-para-explicar.md).
- [F01 y F02: empleados y excepción](docs/avance-f01-f02.md).
- [F03 a F07: cuadrante, reglas y contrato](docs/avance-f03-f07.md).
- [F08 y F09: candidatos y relleno](docs/avance-f08-f09.md).

Las vistas heredan de `layout.blade.php`. Los enlaces usan rutas con nombre, los formularios POST incluyen CSRF y los datos se muestran escapados con `{{ }}`. Las hojas de estilo se sirven desde `public/css`, sin dependencias de frontend.

El código se ha desarrollado con ayuda de IA. Las preguntas PR01–PR03 y el vídeo forman parte de la entrega y deben explicar esta implementación. El control de estilo PHPCS/PSR-12 debe verificarse aparte antes de afirmar que se cumple el extra del enunciado.
