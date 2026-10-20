<?php

declare(strict_types=1);

namespace App\Stores;

use App\Contracts\RosterStore;

/**
 * F06 · TU implementación. El cuadrante vive en la sesión, así que lo que montes
 * sigue ahí al recargar la página.
 *
 * Las cabeceras están puestas para que la clase cumpla el contrato desde el
 * primer momento; el cuerpo es tuyo. Borra los `throw` a medida que los escribas.
 *
 * F07 · Y este comentario también es tuyo: sustitúyelo por el PHPDoc de tu
 * implementación — dónde guarda el cuadrante, con qué clave de sesión y qué
 * devuelve cuando la sesión está vacía. Toma de modelo el de RosterStore.
 */
final class SessionRosterStore implements RosterStore
{
    public function all(): array
    {
        throw new \LogicException('TODO F06: devuelve el cuadrante guardado en la sesión.');
    }

    public function assign(string $day, string $shift, int $employeeId): void
    {
        throw new \LogicException('TODO F06');
    }

    public function remove(string $day, string $shift): void
    {
        throw new \LogicException('TODO F06');
    }

    public function forEmployee(int $employeeId): array
    {
        throw new \LogicException('TODO F06');
    }

    public function title(): string
    {
        throw new \LogicException('TODO F06');
    }

    public function setTitle(string $title): void
    {
        throw new \LogicException('TODO F06');
    }
}
