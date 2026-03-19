<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Después de clonar el repositorio 

Ubicado en la carpeta del proyecto, ejecuta los siguientes comandos

```
composer update
```

```
npm install
```

```
npm run build
```

Borrar caché
```
php artisan optimize:clear
```

### Obten el .env con el equipo de desarrollo

Ejecuta migraciones y seeders (cuando la BD es nueva)

```
php artisan migrate
```

```
php artisan db:seed
```

Si ya existe la BD ejecuta:
```
php artisan migrate:fresh --seed
```

Crea un enlace simbólico en la carpeta pública (opcional)

```
php artisan storage:link
```

Ya puedes ejecutar

```
php artisan serve
```

Y accede a http://127.0.0.1:8000.

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