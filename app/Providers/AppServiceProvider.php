<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * F06 · Aquí, y SOLO aquí, se decide qué implementación se usa.
     *
     * Esta es la única línea que cambias para pasar de una a otra. Si para que
     * la aplicación siga funcionando tienes que tocar además un controlador o
     * una vista, entonces alguien más está decidiendo — y eso es justo lo que
     * F06 evalúa que no ocurra.
     */
    public function register(): void
    {
        // $this->app->bind(
        //     \App\Contracts\RosterStore::class,
        //     \App\Stores\SessionRosterStore::class,   // ← la tuya
        //     // \App\Stores\ArrayRosterStore::class,  // ← la de la plantilla
        // );
    }

    public function boot(): void
    {
        //
    }
}
