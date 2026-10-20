<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\RosterStore;
use App\Stores\ArrayRosterStore;
use Tests\TestCase;

/**
 * F06 · El contrato. PRUEBA VISIBLE.
 *
 * La prueba oculta hace lo mismo con una implementación que no has visto: si tu
 * controlador depende de una clase concreta, aquí pasa y allí falla.
 */
final class F06_ContractTest extends TestCase
{
    public function test_el_contenedor_resuelve_la_interfaz(): void
    {
        $store = $this->app->make(RosterStore::class);

        self::assertInstanceOf(RosterStore::class, $store);
    }

    public function test_la_aplicacion_funciona_con_la_implementacion_de_la_plantilla(): void
    {
        // Cambiar la implementación NO debe requerir tocar controladores ni vistas.
        $this->app->bind(RosterStore::class, ArrayRosterStore::class);

        $this->get(route('roster.index'))->assertOk();
        $this->get(route('roster.summary'))->assertOk();
    }
}
