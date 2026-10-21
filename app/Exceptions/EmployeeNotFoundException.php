<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class EmployeeNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("No existe ningún empleado con el identificador {$id}.");
    }
}
