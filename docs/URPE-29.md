# URPE-29 — UAT y preparación de despliegue V1

## Resultado

URPE V1 queda preparada para una aceptación de usuario y despliegue controlados mediante una matriz UAT reproducible, criterios Go/No-Go, smoke test, rollback y un preflight ejecutable de configuración de producción.

Este ticket no afirma que la UAT humana ya fue ejecutada ni que una instancia de producción ya fue publicada. Esas acciones requieren acceso al entorno objetivo, responsables humanos y evidencia operativa.

## Cambios

- Nuevo comando `php artisan urpe:preflight` para validar el baseline mínimo antes de liberar producción.
- El preflight revisa PHP 8.4+, entorno `production`, debug desactivado, HTTPS, APP_KEY, MySQL, sesiones persistentes, cookie segura/HttpOnly/SameSite, disco clínico privado fuera de `public/`, directorios escribibles y conectividad DB.
- `--skip-db` se limita a pruebas/diagnóstico y no constituye evidencia válida de GO en producción.
- Nueva cobertura automatizada para rechazar una configuración insegura y aceptar una baseline segura.
- Nuevo runbook `docs/UAT_DEPLOYMENT.md` con 20 escenarios UAT de V1, evidencia requerida, perfiles, Go/No-Go, secuencia de despliegue, smoke test y rollback.
- El procedimiento reutiliza `docs/BACKUP_RESTORE.md` y no duplica su política de recuperación.

## Decisiones

- La aceptación humana se mantiene explícitamente separada de CI y pruebas automatizadas.
- No se crean mecanismos de despliegue que almacenen secretos ni credenciales.
- `migrate:rollback` no se considera sustituto de un backup consistente para volver atrás en producción.
- Código, base de datos y archivos clínicos privados se tratan como un mismo punto de recuperación.
- WhatsApp, integraciones adicionales y funciones administrativas/financieras permanecen en V1.x/V2.

## Estado de V1

Con este bloque queda completo el trabajo de ingeniería previsto para la V1. El paso operativo posterior es ejecutar la UAT humana con el SHA aprobado y, una vez firmada la evidencia Go, desplegar ese mismo SHA siguiendo el runbook.

## Validación esperada

- `php artisan test`
- `npm run build`
- `php artisan urpe:preflight --skip-db` en CI/pruebas del comando
- `php artisan urpe:preflight` en el servidor objetivo antes de declarar GO
- CI de rama y PR verdes
- rama consolidada a un único commit antes del PR
