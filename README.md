<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Sobre este proyecto
Este proyecto utiliza Laravel Sail para facilitar el levantamiento del entorno de desarrollo mediante contenedores Docker.

## Comenzando

Para comenzar con este proyecto de Laravel, sigue estos pasos:

1. **Inicia los contenedores Docker usando Laravel Sail:**
   ```bash
   ./vendor/bin/sail up -d
   ```
   Esto iniciará la aplicación y sus dependencias (por ejemplo, MySQL, Redis) en contenedores Docker.

2. **Ejecuta las migraciones de la base de datos:**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```
   Esto creará las tablas necesarias en la base de datos.

3. **Accede a la aplicación:**
   Abre tu navegador y navega a `http://localhost` para ver la aplicación.

4. **Otros comandos útiles de Sail:**
   - Ejecutar comandos de Artisan: `./vendor/bin/sail artisan <comando>`
   - Acceder al shell de Sail: `./vendor/bin/sail shell`
   - Detener los contenedores: `./vendor/bin/sail down`
