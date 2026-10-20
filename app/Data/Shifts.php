<?php

declare(strict_types=1);

namespace App\Data;

/**
 * La tabla de turnos. Es el ÚNICO sitio donde están las horas.
 *
 * La regla R02 (descanso mínimo de 12 horas) debe calcularse a partir de aquí.
 * Si escribes a mano qué combinaciones fallan, tu código dejará de ser correcto
 * en cuanto alguien cambie una hora de esta tabla — y cuatro de las optativas
 * reutilizan este cálculo.
 */
final class Shifts
{
    public const DAYS = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

    /** @var array<string, array{start: string, hours: int}> */
    public const TABLE = [
        'manana' => ['start' => '06:00', 'hours' => 8],
        'tarde'  => ['start' => '14:00', 'hours' => 8],
        'noche'  => ['start' => '22:00', 'hours' => 8],
    ];

    /** @return list<string> */
    public static function names(): array
    {
        return array_keys(self::TABLE);
    }

    /** Minuto del día en que empieza el turno (00:00 = 0). */
    public static function startsAt(string $shift): int
    {
        [$h, $m] = array_map('intval', explode(':', self::TABLE[$shift]['start']));

        return $h * 60 + $m;
    }

    /** Minuto (posiblemente > 1440, si cruza la medianoche) en que acaba el turno. */
    public static function endsAt(string $shift): int
    {
        return self::startsAt($shift) + self::TABLE[$shift]['hours'] * 60;
    }

    public static function hours(string $shift): int
    {
        return self::TABLE[$shift]['hours'];
    }

    /** Índice del día dentro de la semana, 0 = lunes. */
    public static function dayIndex(string $day): int
    {
        return (int) array_search($day, self::DAYS, true);
    }
}
