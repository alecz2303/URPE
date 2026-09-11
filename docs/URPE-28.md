# URPE-28 — Regresión, seguridad, rendimiento y respaldo

## Resultado

URPE V1 queda con un bloque de readiness previo a UAT enfocado en huecos reales, sin ampliar el alcance funcional aprobado.

## Cambios

- Middleware web `SecurityHeaders` con `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` y `Permissions-Policy`; HSTS sólo sobre HTTPS.
- Variables explícitas de sesión en `.env.example` y recordatorio de configuración de producción.
- Listado de pacientes paginado a 25 registros manteniendo eager loading de responsables y conteo total.
- Regresiones automáticas para cabeceras de seguridad, HSTS y paginación.
- Runbook `docs/BACKUP_RESTORE.md` para backup/restore de base de datos, archivos clínicos privados y configuración protegida.
- ROADMAP y SECURITY actualizados.

## Auditoría de rendimiento

Se revisaron las superficies de alto uso. Agenda, sesiones clínicas y reportes ya trabajan con cargas acotadas/paginadas y eager loading en sus consultas principales. El hueco concreto detectado fue `PatientController::index`, que utilizaba `get()` y crecía sin límite con la base de pacientes; se sustituyó por paginación server-side.

No se agregaron índices ni optimizaciones especulativas porque no se encontró evidencia suficiente que justificara cambios de esquema dentro de este ticket.

## Seguridad

El hardening HTTP se aplica en el grupo web y no sustituye TLS ni configuración del servidor. `Strict-Transport-Security` sólo se envía cuando Laravel reconoce una solicitud segura, evitando anunciar HSTS sobre HTTP local.

Los adjuntos clínicos continúan en `storage/app/clinical-private`, fuera del disco público. El procedimiento de backup exige preservar ese directorio junto con la base de datos y el `.env` protegido correspondiente al mismo punto de recuperación.

## Validación esperada

- `php artisan test`
- `npm run build`
- CI de rama verde antes del PR
- rama consolidada a un único commit antes del PR
