<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Desarrollo con Docker

La configuración está en `docker/compose.yml`. Ejecuta los siguientes comandos
desde la raíz del proyecto; `--env-file .env` carga las variables locales.

Requisitos: Docker Engine o Docker Desktop con Docker Compose v2 o posterior.
No necesitas instalar PHP, Composer ni MySQL en tu sistema operativo.
PHP 8.4, Apache, Composer 2, MySQL 8.0, phpMyAdmin y Node 22 se ejecutan en contenedores.

1. Si no tienes `.env`, copia `.env.example` a `.env`. Si ya tienes uno,
   conserva su `APP_KEY` y las credenciales del proyecto. Para Docker configura:

   ```dotenv
   APP_URL=http://localhost:8082
   APP_PORT=8082
   PMA_PORT=8083
   CACHE_STORE=file
   SESSION_DRIVER=file
   DB_CONNECTION=mysql
   DB_HOST=laravel_db
   DB_PORT=3306
   DB_DATABASE=repositorio_municipal
   DB_USERNAME=sifom
   DB_PASSWORD=sifom
   ```

   En Linux, ajusta `HOST_UID` y `HOST_GID` en `.env` con los valores de
   `id -u` e `id -g` para que los archivos generados pertenezcan a tu usuario.
   Ambos usan `1000` por defecto.

2. Prepara las carpetas de Laravel, construye la imagen e instala las dependencias
   respetando `composer.lock`:

   ```bash
   mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
   docker compose --env-file .env -f docker/compose.yml build app
   docker compose --env-file .env -f docker/compose.yml run --rm composer install
   docker compose --env-file .env -f docker/compose.yml run --rm node npm ci
   docker compose --env-file .env -f docker/compose.yml run --rm node npm run build
   ```

3. Inicia los servicios:

   ```bash
   docker compose --env-file .env -f docker/compose.yml up -d
   docker compose --env-file .env -f docker/compose.yml exec app php artisan config:clear
   ```

   Si `APP_KEY` está vacío, genera la clave una sola vez:

   ```bash
   docker compose --env-file .env -f docker/compose.yml exec app php artisan key:generate
   ```

4. Crea o actualiza las tablas y el enlace de archivos públicos:

   ```bash
   docker compose --env-file .env -f docker/compose.yml exec app php artisan migrate
   docker compose --env-file .env -f docker/compose.yml exec app php artisan storage:link
   ```

   Los seeders se ejecutan por separado cuando se necesitan datos iniciales:
   `docker compose --env-file .env -f docker/compose.yml exec app php artisan db:seed`.

- Aplicación: http://localhost:8082
- phpMyAdmin: http://localhost:8083; inicia sesión con `DB_USERNAME` y `DB_PASSWORD`.
- MySQL desde tu equipo: `127.0.0.1:3306`; desde Laravel: `laravel_db:3306`.

Puedes cambiar `APP_PORT`, `PMA_PORT` y `FORWARD_DB_PORT` en `.env` si los
puertos están ocupados. Si cambias `APP_PORT`, actualiza también `APP_URL`.
Las contraseñas predeterminadas son para desarrollo local.
MySQL conserva los datos en el volumen `laravel_db_data`; sus variables de
inicialización solo se aplican cuando el volumen está vacío.

El servicio PHP se llama `app`. Si tenías el servicio anterior `laravel_web`,
ejecuta `docker compose --env-file .env -f docker/compose.yml stop laravel_web` **antes de actualizar** `compose.yml`,
o `docker stop repositorio_municipal-laravel_web-1` si ya lo actualizaste,
y luego `docker compose --env-file .env -f docker/compose.yml up -d --remove-orphans` para liberar el puerto y sustituirlo.
Esto conserva el volumen de MySQL.

Usa la URL completa con `http://` y el puerto: `http://localhost:8082`.
Apache sirve `public/`; no hace falta ejecutar `php artisan serve`.
`APP_URL` configura las URLs de Laravel; el puerto publicado lo controla `APP_PORT`.

Si falla el acceso con un error sobre la tabla `cache`, verifica `CACHE_STORE`
(no `CACHE_DRIVER`) y ejecuta `docker compose --env-file .env -f docker/compose.yml exec app php artisan config:clear`.
Las migraciones son necesarias para crear las tablas; arrancar MySQL no las crea.

### Comandos habituales

```bash
# Vite con recarga automática (deja esta terminal abierta)
docker compose --env-file .env -f docker/compose.yml run --rm --service-ports node

# Artisan, Composer y pruebas
docker compose --env-file .env -f docker/compose.yml exec app php artisan migrate:status
docker compose --env-file .env -f docker/compose.yml run --rm composer check-platform-reqs
docker compose --env-file .env -f docker/compose.yml exec app php artisan test

# Evita archivos propiedad de root al generar código desde Linux
docker compose --env-file .env -f docker/compose.yml exec --user www-data app php artisan make:command MiComando

# Logs y apagado (conserva la base de datos)
docker compose --env-file .env -f docker/compose.yml logs -f app laravel_db
docker compose --env-file .env -f docker/compose.yml down
```

Al terminar de usar Vite, ejecuta `docker compose --env-file .env -f docker/compose.yml run --rm node npm run build`.
Si quedó `public/hot` tras interrumpir Vite, elimínalo para usar los recursos compilados.

---

## Filament
Instalación
```
composer require filament/filament:"^5.0"
```

```
php artisan filament:install --panels
```

Recursos
```
​php artisan make:filament-resource [nombre (de acuerdo al modelo)] --generate
```