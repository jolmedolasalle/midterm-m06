<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\RosterStore;
use App\Data\Shifts;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class RosterWorkflowTest extends TestCase
{
    public function test_una_sesion_nueva_tiene_la_semana_completa_y_vacia(): void
    {
        $store = $this->app->make(RosterStore::class);
        $emptyDay = array_fill_keys(Shifts::names(), null);

        self::assertSame(array_fill_keys(Shifts::DAYS, $emptyDay), $store->all());
        self::assertSame('', $store->title());
        self::assertSame([], $store->forEmployee(1));
    }

    public function test_repetir_o_sustituir_una_celda_no_duplica_asignaciones(): void
    {
        $first = ['day' => 'lunes', 'shift' => 'manana', 'employee_id' => 1];
        $this->post(route('roster.assign'), $first)->assertSessionHasNoErrors();
        $this->post(route('roster.assign'), $first)->assertSessionHasNoErrors();
        $this->post(route('roster.assign'), array_replace($first, ['employee_id' => 2]))
            ->assertSessionHasNoErrors();

        $store = $this->app->make(RosterStore::class);
        self::assertSame([], $store->forEmployee(1));
        self::assertSame([['day' => 'lunes', 'shift' => 'manana']], $store->forEmployee(2));

        $this->get(route('roster.index'))
            ->assertOk()
            ->assertViewHas('roster', fn (array $roster) => $roster['lunes']['manana']->id === 2);
    }

    public function test_quitar_persiste_y_su_mensaje_desaparece_al_recargar(): void
    {
        $cell = ['day' => 'martes', 'shift' => 'tarde'];
        $this->post(route('roster.assign'), $cell + ['employee_id' => 3]);

        $this->post(route('roster.remove'), $cell)
            ->assertRedirect(route('roster.index'))
            ->assertSessionHas('status');

        $this->get(route('roster.index'))->assertOk();
        $this->get(route('roster.index'))->assertSessionMissing('status');
        self::assertNull($this->app->make(RosterStore::class)->all()['martes']['tarde']);

        $this->post(route('roster.remove'), $cell)->assertSessionHasNoErrors();
        self::assertSame([], $this->app->make(RosterStore::class)->forEmployee(3));
    }

    #[DataProvider('invalidAssignments')]
    public function test_una_entrada_invalida_no_altera_la_semana(array $changes, string $field): void
    {
        $store = $this->app->make(RosterStore::class);
        $before = $store->all();
        $data = array_replace(['day' => 'lunes', 'shift' => 'manana', 'employee_id' => 1], $changes);

        $this->post(route('roster.assign'), $data)->assertSessionHasErrors($field);

        self::assertSame($before, $store->all());
    }

    /** @return array<string, array{0: array<string, mixed>, 1: string}> */
    public static function invalidAssignments(): array
    {
        return [
            'dia desconocido' => [['day' => 'festivo'], 'day'],
            'dia como array' => [['day' => ['lunes']], 'day'],
            'turno desconocido' => [['shift' => 'refuerzo'], 'shift'],
            'turno como array' => [['shift' => ['manana']], 'shift'],
            'empleado inexistente' => [['employee_id' => 999], 'employee_id'],
            'empleado no numerico' => [['employee_id' => 'Marta'], 'employee_id'],
            'empleado como array' => [['employee_id' => [1]], 'employee_id'],
        ];
    }

    public function test_quitar_un_dia_invalido_no_crea_celdas_nuevas(): void
    {
        $store = $this->app->make(RosterStore::class);
        $before = $store->all();

        $this->post(route('roster.remove'), ['day' => 'festivo', 'shift' => 'manana'])
            ->assertSessionHasErrors('day');

        self::assertSame($before, $store->all());
    }

    public function test_los_avisos_de_horas_y_cobertura_no_bloquean(): void
    {
        // Nerea tiene un contrato de 20 h; tres mañanas suman 24 h, con descanso suficiente.
        foreach (['lunes', 'martes', 'miercoles'] as $day) {
            $this->post(route('roster.assign'), ['day' => $day, 'shift' => 'manana', 'employee_id' => 5])
                ->assertSessionHasNoErrors();
        }

        $this->get(route('roster.summary'))
            ->assertOk()
            ->assertViewHas('employees', fn (array $rows) => $rows[5]['hours'] === 24 && $rows[5]['excess'] === 4)
            ->assertViewHas('emptySlots', fn (array $slots) => count($slots) === 18)
            ->assertViewHas('daysWithoutResponsible', fn (array $days) => count($days) === 7)
            ->assertSee('Exceso de 4 h');
    }

    public function test_el_resumen_se_recalcula_despues_de_quitar_un_turno(): void
    {
        $cell = ['day' => 'lunes', 'shift' => 'manana'];
        $this->post(route('roster.assign'), $cell + ['employee_id' => 1]);

        $this->get(route('roster.summary'))
            ->assertViewHas('employees', fn (array $rows) => $rows[1]['hours'] === 8)
            ->assertViewHas('daysWithoutResponsible', fn (array $days) => !in_array('lunes', $days, true));

        $this->post(route('roster.remove'), $cell);

        $this->get(route('roster.summary'))
            ->assertViewHas('employees', fn (array $rows) => $rows[1]['hours'] === 0)
            ->assertViewHas('emptySlots', fn (array $slots) => count($slots) === 21)
            ->assertViewHas('daysWithoutResponsible', fn (array $days) => in_array('lunes', $days, true));
    }

    public function test_el_titulo_persiste_y_se_muestra_como_texto_en_ambas_paginas(): void
    {
        $title = "<script>alert('turnos')</script>";

        $this->post(route('roster.title'), ['title' => $title])
            ->assertRedirect(route('roster.index'))
            ->assertSessionHasNoErrors();

        foreach (['roster.index', 'roster.summary'] as $route) {
            $this->get(route($route))->assertOk()->assertSee($title)->assertDontSee($title, false);
        }

        self::assertSame($title, $this->app->make(RosterStore::class)->title());
        $this->post(route('roster.title'), ['title' => ''])->assertSessionHasNoErrors();
        self::assertSame('', $this->app->make(RosterStore::class)->title());
    }

    public function test_un_titulo_con_forma_de_array_se_rechaza_sin_romper_la_vista(): void
    {
        $this->from(route('roster.index'))->post(route('roster.title'), ['title' => ['incorrecto']])
            ->assertSessionHasErrors('title');

        $this->get(route('roster.index'))->assertOk();
    }
}
