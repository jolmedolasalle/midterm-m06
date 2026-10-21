<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\RosterStore;
use App\Stores\ArrayRosterStore;
use Tests\TestCase;

final class RosterCandidatesTest extends TestCase
{
    public function test_buscar_candidatos_en_una_semana_vacia_no_modifica_nada(): void
    {
        $store = $this->app->make(RosterStore::class);
        $before = $store->all();

        $this->get(route('roster.candidates', ['day' => 'lunes', 'shift' => 'manana']))
            ->assertOk()
            ->assertViewHas('candidates', fn (array $rows) => array_column($rows, 'id') === range(1, 12));

        self::assertSame($before, $store->all());
    }

    public function test_los_candidatos_respetan_el_descanso_anterior_y_posterior(): void
    {
        $this->post(route('roster.assign'), ['day' => 'lunes', 'shift' => 'noche', 'employee_id' => 3])
            ->assertSessionHasNoErrors();
        $this->post(route('roster.assign'), ['day' => 'miercoles', 'shift' => 'manana', 'employee_id' => 4])
            ->assertSessionHasNoErrors();

        $this->get(route('roster.candidates', ['day' => 'martes', 'shift' => 'tarde']))
            ->assertOk()
            ->assertViewHas('candidates', function (array $rows): bool {
                $ids = array_column($rows, 'id');

                return !in_array(3, $ids, true) && !in_array(4, $ids, true) && in_array(1, $ids, true);
            });
    }

    public function test_la_lista_excluye_a_quien_ya_trabaja_ese_dia(): void
    {
        $this->post(route('roster.assign'), ['day' => 'lunes', 'shift' => 'manana', 'employee_id' => 1]);

        $this->get(route('roster.candidates', ['day' => 'lunes', 'shift' => 'tarde']))
            ->assertOk()
            ->assertViewHas('candidates', fn (array $rows) => !in_array(1, array_column($rows, 'id'), true));
    }

    public function test_el_relleno_recalcula_la_lista_y_elige_al_primer_candidato_actual(): void
    {
        $cell = ['day' => 'lunes', 'shift' => 'manana'];

        $this->get(route('roster.candidates', $cell))
            ->assertViewHas('candidates', fn (array $rows) => $rows[0]->id === 1);

        // Desde otra página se asigna a Marta ese mismo día: ya no puede cubrir la mañana.
        $this->post(route('roster.assign'), ['day' => 'lunes', 'shift' => 'tarde', 'employee_id' => 1]);

        $this->post(route('roster.auto'), $cell)
            ->assertRedirect(route('roster.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        self::assertSame(2, $this->app->make(RosterStore::class)->all()['lunes']['manana']);
    }

    public function test_las_optativas_no_sustituyen_una_celda_que_ya_esta_ocupada(): void
    {
        $cell = ['day' => 'lunes', 'shift' => 'manana'];
        $this->post(route('roster.assign'), $cell + ['employee_id' => 7]);

        $this->get(route('roster.candidates', $cell))
            ->assertRedirect(route('roster.index'))
            ->assertSessionHasErrors('shift');

        $this->post(route('roster.auto'), $cell)
            ->assertRedirect(route('roster.index'))
            ->assertSessionHasErrors('shift');

        self::assertSame(7, $this->app->make(RosterStore::class)->all()['lunes']['manana']);
    }

    public function test_los_avisos_de_contrato_no_excluyen_candidatos_ni_bloquean_el_relleno(): void
    {
        foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'] as $day) {
            $this->post(route('roster.assign'), ['day' => $day, 'shift' => 'manana', 'employee_id' => 1])
                ->assertSessionHasNoErrors();
        }

        $cell = ['day' => 'domingo', 'shift' => 'manana'];

        $this->get(route('roster.candidates', $cell))
            ->assertOk()
            ->assertViewHas('candidates', fn (array $rows) => $rows[0]->id === 1);

        $this->post(route('roster.auto'), $cell)->assertSessionHasNoErrors();
        self::assertSame(1, $this->app->make(RosterStore::class)->all()['domingo']['manana']);

        $this->get(route('roster.summary'))
            ->assertViewHas('employees', fn (array $rows) => $rows[1]['hours'] === 56 && $rows[1]['excess'] === 16);
    }

    public function test_una_consulta_mal_formada_vuelve_al_formulario_sin_error_500(): void
    {
        $this->get(route('roster.candidates', ['day' => ['lunes'], 'shift' => 'manana']))
            ->assertRedirect(route('roster.index'))
            ->assertSessionHasErrors('day');

        $this->get(route('roster.index'))->assertOk();
    }

    public function test_las_optativas_funcionan_al_cambiar_la_implementacion_del_contrato(): void
    {
        $this->app->bind(RosterStore::class, ArrayRosterStore::class);
        $cell = ['day' => 'domingo', 'shift' => 'manana'];

        $this->get(route('roster.candidates', $cell))->assertOk();
        $this->post(route('roster.auto'), $cell)->assertSessionHasNoErrors()->assertRedirect(route('roster.index'));

        // ArrayRosterStore vuelve al ejemplo en la nueva petición, también con las optativas.
        $this->get(route('roster.index'))
            ->assertOk()
            ->assertViewHas('roster', fn (array $roster) => $roster['domingo']['manana'] === null);
    }
}
