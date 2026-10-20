<?php

declare(strict_types=1);

// ICB0006 - proyecto base - la unica prueba que viene puesta.
//
// No comprueba nada tuyo: comprueba que el entorno funciona. Si esta pasa y tu
// codigo falla, el problema es tu codigo. Si esta falla, el problema es el
// contenedor y no hay que seguir buscando en otro sitio.

namespace Tests\Feature;

use Tests\TestCase;

final class SmokeTest extends TestCase
{
    public function test_the_application_answers_on_the_home_page(): void
    {
        $this->get('/')->assertOk();
    }

    /**
     * En esta sesion no hay base de datos, y nada debe depender de una.
     *
     * Se comprueba por lo negativo a proposito: al ejecutar las pruebas,
     * phpunit.xml pone sesion y cache en "array" en vez de en "file", asi que
     * el valor exacto cambia segun donde mires. Lo que no puede cambiar nunca
     * es que ninguno de los tres apunte a "database".
     */
    public function test_nothing_is_wired_to_a_database(): void
    {
        self::assertNotSame('database', config('session.driver'));
        self::assertNotSame('database', config('cache.default'));
        self::assertNotSame('database', config('queue.default'));
    }
}
