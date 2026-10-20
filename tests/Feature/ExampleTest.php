<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Prueba de humo: la aplicación arranca y la raíz lleva al cuadrante.
 *
 * (Sustituye al ExampleTest que genera Laravel, que esperaba un 200 en `/`.
 * Aquí `/` redirige a la rejilla de la semana, así que un 302 es lo correcto.)
 */
final class ExampleTest extends TestCase
{
    public function test_la_raiz_redirige_al_cuadrante(): void
    {
        $this->get('/')->assertRedirect(route('roster.index'));
    }
}
