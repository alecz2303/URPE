# URPE Gestión Clínica

Sistema web para la operación clínica de la Unidad de Rehabilitación Pediátrica y Evolutiva (URPE).

## Estado

- Proyecto Jira: `URPE`
- Repositorio: `alecz2303/URPE`
- Rama estable: `master`
- Rama activa: `URPE-2`
- Stack base: Laravel 13 / PHP 8.4 / MySQL
- Entorno local recomendado: Laragon
- V1: alcance canónico aprobado

## Propósito

Centralizar agenda clínica, pacientes, expediente clínico digital, terapeutas, terapias, bitácoras de sesión, roles/permisos y trazabilidad de acciones sensibles.

## Principios

1. Agenda/calendario es núcleo de V1.
2. Configuración clínica importante no se hardcodea.
3. Seguridad y auditoría se diseñan desde el inicio.
4. `master` solo cambia mediante Pull Request con al menos 1 aprobación.
5. Antes de integrar una tarea se realiza squash, rebase contra `master`, nueva ejecución de tests y el merge/rebase acordado.
6. Jira gobierna el trabajo y Git cuenta la misma historia.
7. No se inicia funcionalidad fuera del alcance aprobado.

## Instalación local con Laragon

El proyecto debe ubicarse en:

```text
C:\laragon\www\URPE
```

Laragon debe servir el directorio público de Laravel:

```text
C:/laragon/www/URPE/public
```

El dominio local esperado es:

```text
http://URPE.test
```

### Base de datos

Crear una base MySQL llamada `urpe` con `utf8mb4` y copiar `.env.example` a `.env`.

Configuración local base:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=urpe
DB_USERNAME=root
DB_PASSWORD=
```

No se versiona `.env` ni credenciales reales.

### Bootstrap

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan test
```

En Bash puede usarse `cp .env.example .env` en lugar de `copy`.

## Testing

La aplicación local y producción usan MySQL. La suite automática usa SQLite en memoria por defecto para aislamiento y velocidad mediante `phpunit.xml`.

> URPE-2 completa la base técnica antes de iniciar funcionalidad clínica de dominio.
