<?php

declare(strict_types=1);

/*
 * Las rutas son tuyas. Dos condiciones:
 *
 *  · todas con nombre, y todos los enlaces de las vistas construidos con
 *    route(): hay una prueba oculta que busca rutas escritas a mano;
 *  · la rejilla (resources/views/roster/index.blade.php) y las pruebas visibles
 *    usan estos nombres, así que tus rutas tienen que llamarse así:
 *
 *      employees.index   employees.show   roster.index
 *      roster.assign     roster.remove    roster.summary
 *
 *    Las dos que cambian el cuadrante, roster.assign y roster.remove, son POST.
 *
 *    Y dos detalles que también comprueban: / redirige a roster.index, y la
 *    ficha de empleado vive en /empleados/{id} y solo acepta ids numéricos.
 *
 * La URL puede estar en castellano y el nombre en inglés: la URL es para el
 * usuario, el nombre es el identificador con el que trabaja tu código.
 */
