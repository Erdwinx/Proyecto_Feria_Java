# FeriaPass Laravel

Migracion funcional del proyecto de feria desde Spring Boot a Laravel.

## Requisitos

- PHP 8.2+
- Composer
- SQLite (incluido en PHP)

## Inicio rapido

1. Instalar dependencias:

```bash
composer install
```

2. Crear/actualizar `.env`:

```bash
copy .env.example .env
php artisan key:generate
```

3. Crear base SQLite y migrar:

```bash
php -r "if (!file_exists('database/database.sqlite')) touch('database/database.sqlite');"
php artisan migrate:fresh --seed
```

4. Levantar aplicacion:

```bash
php artisan serve
```

## Rutas web principales

- `/login`
- `/inicio`
- `/eventos`
- `/noticias`
- `/promociones`
- `/comprar`
- `/mis-boletos`
- `/boletos`
- `/scanner`

## API principal

Publicas:

- `POST /api/customers/register`
- `POST /api/customers/login`
- `GET /api/tickets`
- `POST /api/tickets`
- `GET /api/tickets/{id}`
- `GET /api/tickets/{id}/current-qr`
- `GET /api/scans`
- `POST /api/scan/validate`
- `POST /api/scan/recover`

Protegidas por JWT Bearer:

- `GET /api/customers/me`
- `GET /api/customers/tickets`
- `POST /api/customers/tickets`

## JWT

Variables relevantes en `.env`:

- `APP_JWT_SECRET`: secreto para firmar tokens.
- `APP_JWT_TTL_MINUTES`: tiempo de vida del token en minutos.

## Pruebas

```bash
php artisan test
```
