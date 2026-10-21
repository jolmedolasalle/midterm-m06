<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\RosterStore;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class MinimumRestEdgeCasesTest extends TestCase
{
    #[DataProvider('reverseAssignments')]
    public function test_se_comprueba_el_descanso_con_el_dia_siguiente_ya_asignado(
        string $earlierDay,
        string $earlierShift,
        string $laterDay,
        string $laterShift
    ): void {
        $this->post(route('roster.assign'), [
            'day' => $laterDay, 'shift' => $laterShift, 'employee_id' => 6,
        ])->assertSessionHasNoErrors();

        $store = $this->app->make(RosterStore::class);
        $before = $store->all();

        $this->post(route('roster.assign'), [
            'day' => $earlierDay, 'shift' => $earlierShift, 'employee_id' => 6,
        ])->assertSessionHasErrors('employee_id');

        self::assertSame($before, $store->all());
    }

    /** @return array<string, array{0: string, 1: string, 2: string, 3: string}> */
    public static function reverseAssignments(): array
    {
        return [
            'tarde antes de manana' => ['lunes', 'tarde', 'martes', 'manana'],
            'noche antes de manana' => ['lunes', 'noche', 'martes', 'manana'],
            'noche antes de tarde' => ['lunes', 'noche', 'martes', 'tarde'],
            'sabado noche antes de domingo tarde' => ['sabado', 'noche', 'domingo', 'tarde'],
        ];
    }

    public function test_dos_noches_consecutivas_dejan_16_horas_de_descanso(): void
    {
        foreach (['martes', 'lunes'] as $day) {
            $this->post(route('roster.assign'), ['day' => $day, 'shift' => 'noche', 'employee_id' => 10])
                ->assertSessionHasNoErrors();
        }

        self::assertCount(2, $this->app->make(RosterStore::class)->forEmployee(10));
    }

    public function test_el_domingo_no_se_confunde_con_el_lunes_anterior(): void
    {
        $this->post(route('roster.assign'), ['day' => 'domingo', 'shift' => 'noche', 'employee_id' => 4])
            ->assertSessionHasNoErrors();
        $this->post(route('roster.assign'), ['day' => 'lunes', 'shift' => 'manana', 'employee_id' => 4])
            ->assertSessionHasNoErrors();

        self::assertCount(2, $this->app->make(RosterStore::class)->forEmployee(4));
    }
}
