# Cuadrante, validación, resumen y contrato: F03–F07

## Qué hace cada pieza

- `RosterController` recibe `RosterStore` en su constructor. Consulta el cuadrante, ejecuta las operaciones ya validadas y prepara las vistas. No conoce ninguna implementación concreta.
- `SessionRosterStore` guarda ids y título mediante la sesión de Laravel. El PHPDoc de la clase y sus métodos explica las claves, el formato y el comportamiento cuando está vacía.
- `AssignShiftRequest` comprueba los campos y aplica las reglas `NoOverlap` y `MinimumRest`. El controlador solo recibe datos validados.
- `RemoveShiftRequest` valida la celda que se quiere vaciar. `UpdateWeekTitleRequest` admite un título vacío o un texto de hasta 160 caracteres.
- El componente `shift-cell` y la rejilla del profesor se conservan. El controlador convierte los ids en objetos `Employee` porque ese es el formato que espera el componente.

## R01 y R02: cuándo se rechaza una asignación

R01 busca si ese empleado ya tiene otro turno el mismo día. La propia celda se excluye: asignar de nuevo al mismo empleado no crea una segunda asignación.

R02 transforma cada inicio y final en minutos contados desde el lunes a las 00:00. Utiliza `Shifts::startsAt()` y `Shifts::endsAt()`, por lo que una noche puede acabar al día siguiente. Compara el turno solicitado con los demás turnos del empleado y resta el final del anterior al inicio del posterior. Un intervalo inferior a 720 minutos se rechaza; exactamente 720 minutos se acepta.

No hay una lista de combinaciones de turnos prohibidas. Cambiar los horarios o añadir un turno en `Shifts::TABLE` modifica el cálculo automáticamente. También se comprueban las asignaciones introducidas fuera de orden, por ejemplo un lunes añadido después de un martes ya ocupado.

La semana se interpreta de lunes a domingo. El lunes de este cuadrante es anterior al domingo, no el lunes de la semana siguiente. No se conocen asignaciones de semanas externas.

## R03, R04 y R05: cuándo se avisa

El resumen se calcula de nuevo al abrirlo. No se guardan totales que puedan quedarse desactualizados:

- R03 compara las horas asignadas con las del contrato y señala el exceso.
- R04 muestra cero o una persona en cada celda y cuenta los turnos vacíos.
- R05 muestra los días que todavía no tienen un responsable.

Estos avisos no forman parte de las reglas bloqueantes. Se puede asignar un tercer turno de ocho horas a una persona con contrato de veinte horas si respeta R01 y R02; aparecerá un exceso de cuatro horas en el resumen.

## Sesión, título y mensajes

`roster.week` guarda la semana y `roster.title` guarda el título. Cada navegador conserva su propia sesión. Tras asignar, quitar o cambiar el título, el controlador redirige al cuadrante con un mensaje flash. El mensaje se muestra una vez, mientras que la semana persiste.

El título se guarda como texto y se imprime con `{{ }}` tanto en el cuadrante como en el resumen. Para demostrarlo se puede escribir `<script>alert('turnos')</script>`: debe verse como texto, sin ejecutarse.

## Cómo demostrar el contrato F06

1. Montar alguna asignación con `SessionRosterStore` y recargar: permanece.
2. En `AppServiceProvider`, cambiar únicamente `\App\Stores\SessionRosterStore::class` por `\App\Stores\ArrayRosterStore::class`.
3. Recargar: aparece el ejemplo de la plantilla. Una asignación nueva desaparece al redirigir porque esa implementación solo guarda los datos durante la petición.
4. Volver a `SessionRosterStore` antes de entregar. El cuadrante de la sesión vuelve a estar disponible.

No se cambia la interfaz, ningún controlador ni ninguna vista para hacer esta demostración.

## Pruebas

```bash
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan test
```

Las pruebas de Classroom se mantienen intactas. Se adapta únicamente la comprobación de portada de `SmokeTest`, procedente del proyecto base: ahora comprueba la redirección obligatoria y que el cuadrante responde con 200. Se conserva su comprobación de que la aplicación no utiliza una base de datos.

Las pruebas añadidas comprueban entradas mal formadas, persistencia, sustitución y eliminación, mensajes flash, título escapado, avisos que no bloquean y descanso con asignaciones introducidas en orden inverso.

La portada de demostración `home.blade.php` se retira porque la entrada de la aplicación ahora es el cuadrante. Este bloque no implementa todavía las dos funcionalidades optativas ni sustituye las respuestas PR01–PR03 y el vídeo de la entrega.
