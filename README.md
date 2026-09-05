# URPE Gestión Clínica

Sistema web para la operación clínica de la Unidad de Rehabilitación Pediátrica y Evolutiva (URPE).

## Estado

- Proyecto Jira: `URPE`
- Repositorio: `alecz2303/URPE`
- Rama estable: `master`
- Rama activa: `URPE-4`
- Stack base: Laravel 13 / PHP 8.4 / MySQL
- Entorno local recomendado: Laragon
- V1: alcance canónico aprobado
- URPE-1: cerrado
- URPE-2: cerrado
- URPE-3: autenticación, cerrado
- URPE-4: usuarios, roles y permisos granulares, en validación final

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

### Primer administrador

Después de crear el usuario inicial, el rol administrativo puede asignarse de forma reproducible con:

```bash
php artisan urpe:grant-admin correo@ejemplo.test
```

El comando es idempotente y evita depender de Tinker o de correos hardcodeados en seeders.

## Testing

La aplicación local y producción usan MySQL. La suite automática usa SQLite en memoria por defecto para aislamiento y velocidad mediante `phpunit.xml`.

## Estado técnico estable

URPE-2 dejó integrado en `master` el bootstrap técnico, CI, configuración Laravel, runtime paths y lockfiles reproducibles.

URPE-3 dejó integrada la autenticación segura por sesión, login/logout, protección `guest`/`auth`, vistas base con Tailwind/Vite y pruebas de regresión. El cierre quedó validado en `master` por GitHub Actions Test #13.

URPE-4 incorpora administración de usuarios, roles y permisos granulares mediante un RBAC propio de primera parte. La gestión de cuentas y la gestión de roles están separadas por permisos; las cuentas pueden activarse/desactivarse; una cuenta inactiva no puede iniciar sesión y una sesión ya abierta se invalida en la siguiente solicitud si la cuenta fue desactivada. Existe UI básica para usuarios y roles/permisos y cobertura automatizada de accesos permitidos y denegados. La suite local completa se encuentra en 41/41 pruebas verdes y GitHub Actions en Test #60 verde.

Las reglas clínicas de alcance por paciente/terapeuta se formalizarán cuando existan las entidades de dominio correspondientes; no se concederá acceso clínico global por defecto.
