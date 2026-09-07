# URPE Gestión Clínica

Sistema web para la operación clínica de la Unidad de Rehabilitación Pediátrica y Evolutiva (URPE).

## Estado

- Proyecto Jira: `URPE`
- Repositorio: `alecz2303/URPE`
- Rama estable: `master`
- Rama activa: `URPE-15`
- Stack base: Laravel 13 / PHP 8.4 / MySQL
- Entorno local recomendado: Laragon
- V1: alcance canónico aprobado
- URPE-1 a URPE-14: cerrados e integrados en `master`
- URPE-15: seeder opt-in para sitio de demostración, en curso

## Propósito

Centralizar agenda clínica, pacientes, expediente clínico digital, terapeutas, terapias, bitácoras de sesión, roles/permisos y trazabilidad de acciones sensibles.

## Principios

1. Agenda/calendario es núcleo de V1.
2. Configuración clínica importante no se hardcodea.
3. Seguridad y auditoría se diseñan desde el inicio.
4. `master` solo cambia mediante Pull Request con al menos 1 aprobación.
5. Antes de integrar una tarea se realiza squash/consolidación, sincronización contra `master`, nueva ejecución de tests y rebase merge.
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

## Sitio de demostración

URPE incluye un seeder independiente para preparar una instancia de muestra sin contaminar el `DatabaseSeeder` normal:

```bash
php artisan db:seed --class=DemoSiteSeeder
```

El seeder configura:

- horario del centro de lunes a viernes, 09:00–14:00 y 16:00–18:00;
- Vojta, 40 minutos, 1 terapeuta;
- Bipedestador, 40 minutos, 1 terapeuta;
- Pediasuit, 60 minutos, 2 terapeutas;
- Jonatham Zambrano y cuatro terapeutas genéricos;
- disponibilidad semanal de los terapeutas alineada al horario del centro;
- una cuenta interna activa con rol `Terapeuta` vinculada a cada perfil demo mediante `therapists.user_id`;
- 12 pacientes ficticios con folios generados por la lógica normal de URPE.

Cuentas demo de terapeutas:

- `jonatham.zambrano@demo.urpe.test`
- `terapeuta1@demo.urpe.test`
- `terapeuta2@demo.urpe.test`
- `terapeuta3@demo.urpe.test`
- `terapeuta4@demo.urpe.test`

La contraseña predeterminada del sitio demo es `UrpeDemo2026!`. Puede reemplazarse antes de ejecutar el seeder definiendo `URPE_DEMO_PASSWORD` en el `.env`. Esta credencial es exclusivamente para instancias de demostración y no debe utilizarse en producción.

El vínculo usuario ↔ terapeuta permite identificar qué profesional autenticado corresponde al perfil operativo que participa en la agenda. Esa relación será la base para restringir la futura bitácora/evolución de sesión a los terapeutas autorizados y, cuando corresponda, a las citas en las que estén asignados.

El seeder es opt-in y puede ejecutarse nuevamente sin duplicar sus registros de muestra estables.

## Testing

La aplicación local y producción usan MySQL. La suite automática usa SQLite en memoria por defecto para aislamiento y velocidad mediante `phpunit.xml`.

## Estado técnico estable

URPE-14 dejó integrada en `master` la agenda recurrente semanal, la validación atómica de series, la edición/cancelación por alcance y la disponibilidad asistida de horarios y terapeutas.

Las reglas clínicas de alcance por paciente/terapeuta se seguirán formalizando en los tickets correspondientes; no se concederá acceso clínico global por defecto.
