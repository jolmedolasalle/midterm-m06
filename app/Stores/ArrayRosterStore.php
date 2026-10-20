<?php

declare(strict_types=1);

namespace App\Stores;

use App\Contracts\RosterStore;
use App\Data\Shifts;

/**
 * Implementación de ejemplo, PROVISTA POR LA PLANTILLA. No la modifiques.
 *
 * Guarda el cuadrante en memoria, así que todo lo que escribas se pierde al
 * terminar la petición: siempre vuelve el mismo cuadrante de ejemplo.
 *
 * Está aquí por dos motivos:
 *
 *  1. Es la "otra" implementación de F06. Cuando cambies el enlace del provider
 *     para usar ésta, la aplicación tiene que seguir funcionando **sin que toques
 *     un solo controlador ni una sola vista**. Eso es lo que se puntúa.
 *  2. Es un ejemplo de código escrito contra el mismo contrato que implementa
 *     tu SessionRosterStore. Léela: dos clases distintas que cumplen la misma
 *     interfaz es justamente lo que permite cambiar una por otra.
 */
final class ArrayRosterStore implements RosterStore
{
    /** @var array<string, array<string, int|null>> */
    private array $roster;

    private string $weekTitle = 'Semana de ejemplo (plantilla)';

    public function __construct()
    {
        $this->roster = $this->emptyWeek();

        // Un cuadrante de ejemplo, deliberadamente incompleto: hay turnos sin
        // cubrir y días sin responsable, para que el resumen de F05 tenga algo
        // que avisar desde el primer momento.
        $this->roster['lunes']['manana']   = 1;
        $this->roster['lunes']['tarde']    = 3;
        $this->roster['martes']['manana']  = 2;
        $this->roster['martes']['noche']   = 10;
        $this->roster['miercoles']['tarde'] = 7;
        $this->roster['jueves']['manana']  = 4;
        $this->roster['viernes']['noche']  = 11;
    }

    public function all(): array
    {
        return $this->roster;
    }

    public function assign(string $day, string $shift, int $employeeId): void
    {
        $this->roster[$day][$shift] = $employeeId;   // se pierde al acabar la petición
    }

    public function remove(string $day, string $shift): void
    {
        $this->roster[$day][$shift] = null;
    }

    public function forEmployee(int $employeeId): array
    {
        $shifts = [];

        foreach ($this->roster as $day => $dayShifts) {
            foreach ($dayShifts as $shift => $id) {
                if ($id === $employeeId) {
                    $shifts[] = ['day' => $day, 'shift' => $shift];
                }
            }
        }

        return $shifts;
    }

    public function title(): string
    {
        return $this->weekTitle;
    }

    public function setTitle(string $title): void
    {
        $this->weekTitle = $title;
    }

    /** @return array<string, array<string, int|null>> */
    private function emptyWeek(): array
    {
        $week = [];

        foreach (Shifts::DAYS as $day) {
            foreach (Shifts::names() as $shift) {
                $week[$day][$shift] = null;
            }
        }

        return $week;
    }
}
