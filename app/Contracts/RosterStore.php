<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * El contrato del cuadrante semanal.
 *
 * Quien use esta interfaz no debe necesitar saber dónde se guardan los datos:
 * si al cambiar de implementación tienes que tocar un controlador o una vista,
 * la inversión de dependencias no está hecha (F06).
 *
 * El cuadrante se representa como un mapa de día → turno → id de empleado:
 *
 *     ['lunes' => ['manana' => 3, 'tarde' => null, 'noche' => 7], 'martes' => [...], ...]
 *
 * Los días son `lunes`…`domingo` y los turnos `manana`, `tarde`, `noche`,
 * tal y como los define {@see \App\Data\Shifts}.
 */
interface RosterStore
{
    /**
     * El cuadrante completo de la semana.
     *
     * Devuelve siempre los 7 días con sus 3 turnos; un turno sin cubrir es `null`.
     *
     * @return array<string, array<string, int|null>>
     */
    public function all(): array;

    /**
     * Asigna un empleado a un turno de un día, sustituyendo al que hubiera.
     *
     * No valida las reglas de negocio: eso es trabajo del Form Request (F04).
     */
    public function assign(string $day, string $shift, int $employeeId): void;

    /** Deja vacío un turno. Si ya lo estaba, no hace nada. */
    public function remove(string $day, string $shift): void;

    /**
     * Los turnos que tiene asignados un empleado.
     *
     * @return list<array{day: string, shift: string}>
     */
    public function forEmployee(int $employeeId): array;

    /** El título libre de la semana. Cadena vacía si no se ha puesto ninguno. */
    public function title(): string;

    /** Cambia el título de la semana. */
    public function setTitle(string $title): void;
}
