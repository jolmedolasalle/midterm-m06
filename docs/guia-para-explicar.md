# Guía para entender y explicar el proyecto

## Qué venía dado y qué se ha desarrollado

| Parte | Procedencia y trabajo realizado |
| --- | --- |
| Laravel, Sail y configuración inicial | Proyecto base del curso. |
| `Employee`, `Staff` y `Shifts` | Datos del profesor. En `Staff` se añade `findOrFail()` para la ficha. |
| `RosterStore` | Contrato del profesor, conservado sin cambios. |
| `ArrayRosterStore` | Implementación de ejemplo del profesor, conservada sin cambios. |
| Rejilla y componente `shift-cell` | Plantilla del profesor. La rejilla se integra en el layout y recibe los formularios adicionales. |
| `SessionRosterStore`, reglas y `AssignShiftRequest` | Esqueletos del profesor cuyos cuerpos se han completado. |
| Rutas, controladores, excepción, layout y demás vistas | Código desarrollado para resolver la práctica. |
| F08 y F09 | Funcionalidades optativas desarrolladas sobre las reglas y el contrato anteriores. |
| Pruebas adicionales y documentación | Material de comprobación y explicación del trabajo. |

Los archivos de los parches contienen la solución de programación, no solo instrucciones de instalación. La práctica evalúa que la aplicación funcione y que se entienda por qué está organizada así.

## Una asignación, paso a paso

1. En el navegador se eligen día, turno y empleado.
2. El formulario envía un POST a la ruta con nombre `roster.assign`.
3. `AssignShiftRequest` comprueba los campos y ejecuta `NoOverlap` y `MinimumRest`.
4. Si hay un error, Laravel devuelve el formulario con el motivo. El controlador no llega a guardar.
5. Si los datos son válidos, `RosterController` llama a `assign()` del contrato.
6. Con la configuración de entrega, `SessionRosterStore` actualiza el array y lo guarda en sesión.
7. El controlador redirige al cuadrante y deja un mensaje flash para la siguiente petición.
8. Blade muestra los datos con el layout común y salida escapada.

## El código de guardar, explicado

```php
$week = $this->all();
$week[$day][$shift] = $employeeId;
session()->put(self::WEEK_KEY, $week);
```

La primera línea obtiene la semana actual. La segunda cambia una celda del array. La tercera guarda el array en la sesión. No calcula descansos: esa comprobación ya se ha hecho antes.

## Por qué hay varios archivos

Cada pieza tiene una tarea concreta:

- La ruta relaciona una dirección con una acción.
- El Form Request comprueba los datos enviados.
- Las reglas contienen los cálculos de negocio que se reutilizan.
- El controlador coordina la petición y prepara la respuesta.
- El almacén guarda y recupera el cuadrante.
- La vista presenta la información.

Esta separación y el uso del contrato son requisitos de la práctica. No se han añadido bases de datos, cuentas de usuario, APIs ni herramientas de frontend.

## La parte del descanso que hay que entender

Se comparan momentos de una misma semana, expresados en minutos desde el lunes a las 00:00. El índice del día se multiplica por 1440, que son los minutos de un día, y se suma la hora de la tabla.

La noche del lunes empieza a las 22:00 y termina el martes a las 06:00. Si el siguiente turno empieza el martes a las 14:00, hay ocho horas de descanso. Como son menos de doce, se rechaza.

El cálculo también mira hacia delante. Si primero se asignó el martes y después se intenta añadir el lunes, el resultado debe ser el mismo.

## El contrato, con un ejemplo concreto

El controlador pide un `RosterStore`. Conoce sus seis métodos, pero no sabe dónde se guarda la información. `AppServiceProvider` elige qué implementación entregar.

Con `SessionRosterStore`, el cuadrante permanece al recargar. Con `ArrayRosterStore`, vuelve al ejemplo en cada petición. Para cambiar este comportamiento se modifica una sola línea del provider y no se tocan controladores ni vistas.

## Diferencias que conviene saber explicar

- **Rechazar:** una asignación incumple R01 o R02 y no se guarda.
- **Avisar:** hay horas de más, turnos vacíos o días sin responsable; se permite seguir montando la semana.
- **Responder con 404:** se ha pedido una ficha que no existe. La búsqueda lanza una excepción de datos y el manejador decide la respuesta HTTP.
- **Sesión:** conserva la semana entre peticiones del mismo navegador.
- **Flash:** muestra un mensaje en la siguiente petición y después desaparece.
- **Escapar:** imprime el título como texto para que una etiqueta introducida por el usuario no se ejecute como HTML o JavaScript.

## Las dos optativas en una frase

F08 recorre la plantilla y conserva quienes pasan las dos reglas. F09 repite esa comprobación en el momento de asignar y utiliza el primer candidato. Ambas reutilizan la misma clase de búsqueda.

## Cómo preparar la explicación

No hace falta memorizar cada línea. Sí conviene poder señalar dónde se valida, dónde se guarda, cómo se calcula el descanso y qué cambia al elegir otra implementación. Las pruebas ayudan a comprobar el código, pero no sustituyen esa explicación.

El proyecto se ha desarrollado con ayuda de IA. Para la demostración y las preguntas, las explicaciones deben corresponder a lo que se comprende y a lo que hace realmente el código. Cumplir la rúbrica, pasar las pruebas visibles y explicar bien el trabajo no permite garantizar una nota ni anticipar todos los casos de las pruebas ocultas.
