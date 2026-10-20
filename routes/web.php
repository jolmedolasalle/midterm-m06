<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
 * ICB0006 - Acceso a datos - proyecto base.
 *
 * Aqui solo hay una ruta, y esta para que compruebes que el contenedor
 * responde. Borrala en cuanto escribas las tuyas.
 */

Route::get('/', fn () => view('home'))->name('home');
