# Optativas elegidas: F08 y F09

Se implementan exactamente dos funcionalidades optativas.

## F08: quién puede cubrir un turno

En el cuadrante, el formulario «¿Quién puede cubrir un turno vacío?» permite elegir día y turno. La consulta usa GET: muestra información y no modifica asignaciones.

`EmptyShiftRequest` comprueba que el día y el turno existen y que la celda está vacía. Después, `ShiftCandidates::forSlot()` recorre los empleados de `Staff::all()` y utiliza las mismas reglas `NoOverlap` y `MinimumRest` de la asignación manual. Devuelve solo los que pasan ambas.

Los avisos R03, R04 y R05 no excluyen candidatos. Por ejemplo, haber superado las horas de contrato produce un aviso en el resumen, pero no bloquea esta búsqueda.

## F09: rellenar un turno vacío

Desde la lista de candidatos, el botón «Asignar al primer candidato» envía un POST con protección CSRF. La aplicación vuelve a comprobar que la celda sigue vacía y recalcula los candidatos. No confía en una lista consultada anteriormente.

Se elige el primero según el orden fijo de `Staff::all()`, es decir, el orden de la plantilla, no al azar ni según el filtro del listado de empleados. Si no hubiera candidatos, se devuelve un mensaje sin cambiar el cuadrante. Una celda ocupada tampoco se sustituye desde esta función.

La escritura se hace a través de `RosterStore`, por lo que estas optativas también funcionan al cambiar la implementación en el provider.

## Por qué hay una clase compartida

`ShiftCandidates` es una clase PHP con un método y un bucle. Se separa para que la consulta y la asignación automática utilicen exactamente la misma selección de empleados. Los cálculos de solapamiento y descanso siguen estando en las dos reglas originales.

## Demostración breve

1. En una celda vacía, buscar candidatos: la consulta no modifica el cuadrante.
2. Asignar manualmente a Marta un turno de ese día y buscar candidatos para otro turno vacío del mismo día: Marta queda fuera por R01.
3. Pulsar «Asignar al primer candidato» y comprobar en el cuadrante que aparece la persona indicada.
4. Intentar buscar candidatos para esa misma celda ya ocupada: se explica que hay que elegir una vacía.

Para demostrar R02, asignar a Laia el lunes de noche y consultar candidatos para el martes de tarde: no debe aparecer, porque solo habría ocho horas de descanso.

## Pruebas

```bash
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan test
```

`RosterCandidatesTest` comprueba la consulta sin escrituras, las dos reglas bloqueantes, la selección actualizada, las celdas ocupadas, los avisos que no bloquean, las entradas mal formadas y el funcionamiento con `ArrayRosterStore`.
