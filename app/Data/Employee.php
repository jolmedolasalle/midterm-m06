<?php

declare(strict_types=1);

namespace App\Data;

final class Employee
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $role,          // responsable | dependiente | almacen
        public readonly int $contractHours,    // 20 | 30 | 40
    ) {
    }
}
