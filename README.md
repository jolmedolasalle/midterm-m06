# Proyecto base — ICB0006 · Acceso a datos

Laravel 13 sobre PHP 8.4, con **Laravel Sail** y **sin base de datos**. Es el punto
de partida de todo lo que harás en la sesión S02: de aquí sale la aplicación que se
construye en vídeo, y de aquí salen también tus proyectos de los ejercicios.

No hace nada todavía. Esa es la idea.

---

## Arrancar

**Lo único que necesitas instalado es Docker.** PHP, Composer y las extensiones
vienen dentro de la imagen; no instales nada más.

> ### En Windows: la terminal va dentro de WSL2
>
> `./vendor/bin/sail` es un script de bash que **no arranca desde PowerShell ni
> desde Git Bash**: aborta con *"Unsupported operating system"*. Una sola vez:
>
> 1. `wsl --install -d Ubuntu` desde PowerShell, y reinicia si te lo pide.
> 2. **Abre Ubuntu y termina de configurarla** — te pedirá un usuario y una
>    contraseña de Linux. Hasta que no lo hagas, la distribución no está lista
>    aunque figure como instalada.
> 3. Docker Desktop → *Settings → Resources → WSL integration* → activa esa
>    distribución → *Apply & restart*.
>
> A partir de ahí, trabaja siempre desde la terminal de Ubuntu. En macOS y Linux
> no hay nada de esto.

Primero, una sola vez, para que exista `vendor/` y con él el propio `sail`:

```bash
docker run --rm -e COMPOSER_PROCESS_TIMEOUT=0 -v "$(pwd):/var/www/html" -w /var/www/html \
  laravelsail/php84-composer:latest \
  composer install

cp .env.example .env
```

Y a partir de aquí, siempre:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

Abre <http://localhost>. Tienes que ver una página que dice **"El contenedor
responde"**. Si la ves, tu entorno está bien y puedes empezar.

> **La primera construcción tarda entre cinco y diez minutos**, porque no descarga
> una imagen: la construye. Las siguientes son segundos.

### El atajo que querrás

En WSL (bash):

```bash
echo "alias sail='sh \$([ -f sail ] && echo sail || echo vendor/bin/sail)'" >> ~/.bashrc
source ~/.bashrc
```

En macOS la terminal usa zsh, que no lee `~/.bashrc`:

```bash
echo "alias sail='sh \$([ -f sail ] && echo sail || echo vendor/bin/sail)'" >> ~/.zshrc
source ~/.zshrc
```

**Regla mental: lo que antes empezaba por `php`, ahora empieza por `sail`.**

| Antes | Aquí |
|---|---|
| `php artisan route:list` | `sail artisan route:list` |
| `php artisan make:controller X` | `sail artisan make:controller X` |
| `php artisan test` | `sail test` |
| `composer require X` | `sail composer require X` |

---

## Qué hay dentro

| Dónde | Qué es |
|---|---|
| `routes/web.php` | Una ruta, la de la página de prueba. Bórrala cuando escribas las tuyas |
| `resources/views/home.blade.php` | Esa página. Es lo primero que vas a sustituir |
| `public/css/app.css` | El estilo, servido sin compilar nada |
| `compose.yaml` | El contenedor: PHP 8.4 y **ningún servicio más** |
| `.env.example` | La plantilla de configuración. **`.env` no se sube al repositorio, `.env.example` sí** |
| `tests/` | Vacío de pruebas, con la infraestructura lista para las tuyas |

---

## Para empezar tu proyecto

Copia esta carpeta con el nombre que le toque, entra en ella y cámbiale el
nombre a la aplicación en tu `.env`:

```
APP_NAME=Club
```

Arráncala igual que arriba:

```bash
sail up -d
sail artisan key:generate
```

Si la base sigue arrancada, párala antes con `sail down` desde su carpeta: las
dos quieren el puerto 80, y solo una puede tenerlo.

Haz un commit en cuanto la página de prueba te responda. Ese es tu punto cero.
