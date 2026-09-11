# UAT y despliegue V1 — URPE Gestión Clínica

## Objetivo

Cerrar V1 con una aceptación humana verificable y un procedimiento de despliegue/rollback reproducible. Este documento no sustituye las pruebas automatizadas ni el runbook de backup/restore de `docs/BACKUP_RESTORE.md`.

## Release candidate

Antes de iniciar UAT registrar:

- SHA exacto de `master` candidato;
- número de PR/ticket asociado;
- fecha y responsable de la ejecución;
- ambiente UAT utilizado;
- backup de referencia y fecha de la última restauración de prueba;
- resultado de `php artisan test`;
- resultado de `npm run build`;
- resultado de `php artisan urpe:preflight` en el entorno objetivo.

No iniciar UAT sobre una rama de trabajo ni sobre un SHA diferente al que se pretende desplegar.

## Matriz UAT

Cada escenario debe registrar **Aprobado / Rechazado / Bloqueado**, responsable, fecha, evidencia breve e incidencia asociada cuando aplique.

| ID | Perfil | Flujo | Criterio de aceptación |
| --- | --- | --- | --- |
| UAT-01 | Administrador | Login/logout | Puede iniciar/cerrar sesión; credenciales inválidas no revelan si la cuenta existe. |
| UAT-02 | Administrador | Usuarios y roles | Puede administrar cuentas/roles según permisos; no puede desactivar su propia cuenta. |
| UAT-03 | Coordinación Clínica | Configuración del centro | Puede consultar/actualizar configuración y horarios válidos; traslapes son rechazados. |
| UAT-04 | Coordinación Clínica | Terapeutas | Alta de terapeuta crea acceso asociado, disponibilidad y rol; desactivación preserva historial. |
| UAT-05 | Recepción | Pacientes/responsables | Puede crear/editar pacientes y responsables sin acceder a contenido clínico no autorizado. |
| UAT-06 | Coordinación Clínica | Expediente basal | Puede crear/actualizar expediente; texto clínico no aparece en auditoría operativa. |
| UAT-07 | Coordinación Clínica | Archivos clínicos | Puede cargar/descargar/retirar archivo autorizado; URL directa pública no expone el archivo. |
| UAT-08 | Recepción/Coordinación | Agenda | Puede crear, reprogramar y cancelar citas respetando horarios, disponibilidad y traslapes. |
| UAT-09 | Recepción/Coordinación | Recurrencias | Serie semanal válida se crea completa; un conflicto impide creación parcial. |
| UAT-10 | Recepción/Coordinación | Estados de cita | Transiciones válidas funcionan y estados terminales impiden acciones incompatibles. |
| UAT-11 | Terapeuta | Sesión clínica | Sólo ve/captura sesiones asignadas o históricamente relacionadas conforme a permisos. |
| UAT-12 | Terapeuta | Workspace/autoguardado | Borrador persiste, puede continuar y completar; una sesión completada queda inmutable. |
| UAT-13 | Terapeuta/Coordinación | Enmienda | Corrección posterior crea enmienda append-only sin alterar la nota original. |
| UAT-14 | Coordinación/Administrador | Sustitución | Reasignación urgente valida disponibilidad y conserva historial del terapeuta sustituido. |
| UAT-15 | Consulta/Dirección | Consulta | Puede consultar únicamente las superficies permitidas y no obtiene capacidades de edición. |
| UAT-16 | Coordinación/Consulta | Reportes | Filtros y KPIs operativos corresponden a los registros y no exponen narrativa clínica. |
| UAT-17 | Todos los perfiles | Navegación responsive | Sidebar/menú móvil, agenda día/semana/mes y formularios críticos son utilizables sin campos ilegibles. |
| UAT-18 | Usuario sin permiso | Seguridad | URLs directas y operaciones mutables sensibles son denegadas aunque conozca la ruta. |
| UAT-19 | Administrador | Backup/restore | Se ejecuta una restauración de prueba y el checklist de `BACKUP_RESTORE.md` queda aprobado. |
| UAT-20 | Administrador/Coordinación | Smoke final | Dashboard, agenda, pacientes, expediente, sesión, archivo y reportes cargan sin errores nuevos. |

## Evidencia UAT

Para cada ID registrar como mínimo:

- resultado;
- usuario/rol de prueba;
- datos ficticios utilizados;
- captura o nota breve del resultado esperado/obtenido;
- ticket de incidencia si falla;
- SHA probado.

No copiar narrativa clínica real ni datos personales reales a tickets, capturas públicas o repositorios.

## Criterios Go / No-Go

### GO

El despliegue puede continuar únicamente si:

- UAT-01 a UAT-20 están aprobados o existe una excepción formal documentada para un punto no crítico;
- no hay defectos abiertos de severidad crítica/alta que afecten seguridad, pérdida de datos, autorización, agenda o expediente;
- CI del SHA candidato está verde;
- `php artisan urpe:preflight` termina con código 0 en producción;
- backup previo al despliegue está confirmado;
- existe ventana de rollback y responsable disponible.

### NO-GO

Detener el despliegue si ocurre cualquiera de estos casos:

- pérdida/inconsistencia de datos o archivos clínicos;
- bypass de autorización;
- `APP_DEBUG=true`, HTTP sin TLS o cookie de sesión no segura en producción;
- migración fallida o conexión DB inestable;
- almacenamiento clínico apunta a `public/` o no es escribible;
- pruebas críticas de agenda/sesión/expediente fallan;
- no existe backup restaurable del punto previo.

## Preflight de producción

Ejecutar desde la raíz de la aplicación:

```bash
php artisan urpe:preflight
```

El comando valida como mínimo PHP 8.4+, entorno production, debug desactivado, HTTPS, APP_KEY, MySQL, sesión en base de datos, cookie segura/HttpOnly/SameSite, disco clínico privado fuera de `public/`, permisos de escritura y conectividad DB.

`--skip-db` existe sólo para pruebas automatizadas o diagnóstico aislado; no debe usarse como evidencia de GO en producción.

## Secuencia de despliegue V1

1. Confirmar SHA objetivo y CI verde.
2. Confirmar backup consistente de base de datos + `storage/app/clinical-private` + `.env` protegido, siguiendo `docs/BACKUP_RESTORE.md`.
3. Activar mantenimiento: `php artisan down`.
4. Desplegar el SHA exacto aprobado de `master`.
5. Instalar dependencias PHP: `composer install --no-dev --optimize-autoloader --no-interaction`.
6. Instalar/build frontend en el pipeline o servidor de build: `npm ci --no-audit --no-fund` y `npm run build`.
7. Confirmar `.env` de producción: `APP_ENV=production`, `APP_DEBUG=false`, URL HTTPS, `SESSION_SECURE_COOKIE=true`, credenciales correctas y misma `APP_KEY` de la instancia.
8. Limpiar caches heredadas: `php artisan optimize:clear`.
9. Ejecutar migraciones/backfills versionados: `php artisan migrate --force`.
10. Reconstruir caches de producción cuando el hosting lo permita: `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`.
11. Ejecutar `php artisan urpe:preflight`. Debe terminar en 0.
12. Verificar permisos de `storage/`, `bootstrap/cache` y `storage/app/clinical-private`.
13. Reactivar: `php artisan up`.
14. Ejecutar smoke test post-deploy.
15. Revisar logs antes de declarar GO definitivo.

## Smoke test post-deploy

Con datos de prueba controlados:

1. `/up` responde correctamente.
2. Login y logout funcionan.
3. Dashboard carga sin error 500.
4. Agenda día/semana/mes abre y conserva filtros/navegación.
5. Pacientes abre y pagina correctamente.
6. Expediente autorizado abre; perfil no autorizado recibe denegación.
7. Sesiones clínicas abre y un borrador existente puede continuar.
8. Archivo clínico privado autorizado descarga por backend; acceso no autorizado se deniega.
9. Reportes cargan para perfil autorizado.
10. No aparecen errores nuevos críticos en `storage/logs`.

## Rollback

Un rollback debe tratar código, base de datos y archivos clínicos como un mismo punto de recuperación.

1. `php artisan down`.
2. Registrar el motivo y SHA fallido.
3. Restaurar el SHA anterior conocido como estable.
4. Restaurar base de datos desde el backup previo si el despliegue ejecutó migraciones o escribió datos incompatibles. No intentar `migrate:rollback` a ciegas como sustituto del backup.
5. Restaurar `storage/app/clinical-private` desde el mismo punto temporal si hubo cambios de archivos durante la ventana.
6. Restaurar el `.env` previo sólo si fue modificado, preservando APP_KEY.
7. Reinstalar dependencias/caches del SHA restaurado.
8. Ejecutar `php artisan urpe:preflight`.
9. `php artisan up`.
10. Ejecutar smoke test y documentar el resultado.

## Cierre de UAT

El ticket de release sólo puede cerrarse cuando exista evidencia humana de UAT. La integración de este documento y del preflight deja **V1 lista para UAT/despliegue**, pero no debe interpretarse como UAT humana ya ejecutada ni como producción ya desplegada.
