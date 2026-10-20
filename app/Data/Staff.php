<?php

declare(strict_types=1);

namespace App\Data;

/** La plantilla del comercio. Datos fijos: no se añaden ni se borran empleados. */
final class Staff
{
    public const ROLES = ['responsable', 'dependiente', 'almacen'];

    /** @return list<Employee> */
    public static function all(): array
    {
        return array_map(
            static fn (array $r) => new Employee($r[0], $r[1], $r[2], $r[3]),
            [
                [1,  'Marta Ferrer',    'responsable', 40],
                [2,  'Jordi Vidal',     'responsable', 40],
                [3,  'Laia Puig',       'dependiente', 30],
                [4,  'Óscar Ramírez',   'dependiente', 40],
                [5,  'Nerea Company',   'dependiente', 20],
                [6,  'Iván Sotelo',     'dependiente', 30],
                [7,  'Carla Benítez',   'dependiente', 40],
                [8,  'Hugo Marín',      'dependiente', 20],
                [9,  'Aitana Roig',     'dependiente', 30],
                [10, 'Pau Esteve',      'almacen',     40],
                [11, 'Rocío Lantero',   'almacen',     30],
                [12, 'Dani Quintana',   'almacen',     20],
            ],
        );
    }

    /** @return list<Employee> */
    public static function byRole(?string $role): array
    {
        $all = self::all();

        return $role === null ? $all : array_values(array_filter($all, static fn ($e) => $e->role === $role));
    }

    public static function find(int $id): ?Employee
    {
        foreach (self::all() as $employee) {
            if ($employee->id === $id) {
                return $employee;
            }
        }

        return null;
    }
}
