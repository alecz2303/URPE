# Backup y restauración — URPE Gestión Clínica

## Objetivo

Este procedimiento define el mínimo operativo para recuperar una instalación V1 de URPE sin depender de copias parciales ni de credenciales almacenadas en el repositorio.

## Qué debe respaldarse

1. **Base de datos MySQL completa**: estructura, datos, tablas de autorización, sesiones, auditoría, agenda, expediente y metadatos de archivos.
2. **Archivos clínicos privados**: directorio `storage/app/clinical-private` completo.
3. **Archivo `.env` de producción**: conservarlo fuera del repositorio, protegido y con acceso restringido. Incluye `APP_KEY`, credenciales de base de datos y configuración del entorno.
4. **Código desplegado y referencia de versión**: el repositorio Git es la fuente reproducible; registrar el SHA desplegado.
5. **Dependencias reproducibles**: conservar `composer.lock` y `package-lock.json` en Git. No es necesario respaldar `vendor/` ni `node_modules/` si el entorno puede reinstalarlos.

## Frecuencia mínima recomendada

- Base de datos: diaria.
- `storage/app/clinical-private`: diaria y adicionalmente antes de mantenimientos de infraestructura.
- `.env`: cada vez que cambie configuración o credenciales.
- Antes de una actualización: crear un punto de restauración de base de datos + archivos clínicos y registrar el SHA actual.

La frecuencia definitiva debe ajustarse a la política operativa y al volumen real de atención.

## Reglas de seguridad

- Las copias deben almacenarse fuera del `public/` del sitio.
- No dejar dumps SQL ni ZIP de archivos clínicos accesibles por HTTP.
- Proteger el destino con cifrado o controles equivalentes de acceso de la infraestructura.
- Restringir acceso a las copias a personal autorizado.
- No enviar backups clínicos por correo, mensajería instantánea ni enlaces públicos.
- La restauración debe realizarse en un entorno controlado; una copia de producción no debe usarse como dataset de desarrollo sin anonimización y autorización correspondientes.

## Procedimiento de respaldo — hosting/cPanel

### Base de datos

Usar la herramienta de respaldo de cPanel o phpMyAdmin para exportar la base MySQL completa. Verificar que el archivo generado no esté vacío y registrar fecha/hora.

Si existe acceso shell seguro, puede utilizarse `mysqldump` con credenciales suministradas por el entorno, nunca escritas dentro del repositorio.

### Archivos clínicos

Desde el administrador de archivos o mediante la herramienta de backup del hosting, copiar íntegramente:

`storage/app/clinical-private`

No reemplazar este directorio por `storage/app/public`: los adjuntos clínicos no viven en el disco público.

### Configuración

Guardar una copia protegida del `.env` activo. Registrar además:

- versión de PHP;
- versión de MySQL/MariaDB;
- document root configurado hacia `public/`;
- SHA desplegado de `master`;
- tareas cron/queue configuradas, si existen en la instalación.

## Procedimiento de restauración

1. Poner la aplicación en mantenimiento o bloquear acceso externo mientras se recupera la instancia.
2. Desplegar el SHA objetivo del repositorio.
3. Instalar dependencias con `composer install --no-dev --optimize-autoloader` y construir assets de producción conforme al proceso de despliegue vigente.
4. Restaurar el `.env` protegido y comprobar que `APP_KEY` sea exactamente el de la instancia recuperada. No generar una clave nueva durante una restauración normal.
5. Crear/restaurar la base de datos desde el dump validado.
6. Ejecutar `php artisan migrate --force` únicamente después de confirmar que el código desplegado corresponde al punto objetivo y que la copia previa está disponible.
7. Restaurar `storage/app/clinical-private` preservando nombres físicos y estructura.
8. Limpiar y reconstruir caches de Laravel conforme al entorno (`optimize:clear` y cacheo de producción cuando aplique).
9. Verificar permisos de escritura sobre `storage/` y `bootstrap/cache` sin ampliar permisos más de lo necesario.
10. Retirar modo mantenimiento sólo después de completar el checklist post-restore.

## Checklist post-restore

- La aplicación carga mediante HTTPS y `APP_DEBUG=false`.
- Login funciona y una sesión puede cerrarse correctamente.
- Roles/permisos se conservan.
- Se puede abrir agenda y consultar pacientes.
- Un expediente clínico autorizado abre correctamente.
- Una bitácora existente conserva contenido, participantes y estado.
- Un archivo clínico existente puede descargarse sólo con autorización y su hash/metadata siguen asociados.
- Un usuario sin permiso continúa recibiendo denegación en rutas clínicas sensibles.
- Reportes cargan para perfiles autorizados.
- `/up` responde correctamente.
- No hay errores nuevos en logs después de las pruebas de humo.

## Prueba de restauración

Una copia no se considera confiable sólo porque el archivo exista. Antes de UAT/despliegue inicial y después de cambios relevantes de infraestructura debe realizarse al menos una restauración de prueba en un entorno aislado, documentando:

- fecha;
- backup utilizado;
- SHA restaurado;
- responsable;
- resultado del checklist;
- incidencias encontradas.

## Retención

La política final de retención debe definirse con el responsable operativo/legal del servicio. Técnicamente, URPE requiere conservar juntos el dump de base de datos y los archivos clínicos correspondientes al mismo punto temporal para evitar referencias huérfanas o metadatos sin binario.
