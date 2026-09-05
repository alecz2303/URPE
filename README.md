# URPE Gestión Clínica

Sistema web para la operación clínica de la Unidad de Rehabilitación Pediátrica y Evolutiva (URPE).

## Estado

- Proyecto Jira: `URPE`
- Repositorio: `alecz2303/URPE`
- Rama estable: `master`
- Rama activa: `URPE-1`
- Stack base: Laravel 13 / PHP 8.4
- V1: en definición canónica

## Propósito

Centralizar agenda clínica, pacientes, expediente clínico digital, terapeutas, terapias, bitácoras de sesión, roles/permisos y trazabilidad de acciones sensibles.

## Principios

1. Agenda/calendario es núcleo de V1.
2. Configuración clínica importante no se hardcodea.
3. Seguridad y auditoría se diseñan desde el inicio.
4. `master` solo cambia mediante Pull Request con al menos 1 aprobación.
5. Jira gobierna el trabajo y Git cuenta la misma historia.
6. No se inicia funcionalidad fuera del alcance aprobado.

## Instalación base

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan test
```

> Este bloque inicial define la base documental y técnica. No contiene aún funcionalidad clínica de negocio.
