# Security — URPE Gestión Clínica

URPE procesará información clínica y datos personales; seguridad es requisito transversal.

## Baseline

- Contraseñas mediante hashing provisto por Laravel.
- CSRF activo en formularios web.
- Sesiones seguras y cookies adecuadas al ambiente.
- Validación server-side.
- Autorización server-side en cada operación sensible.
- Roles + permisos granulares.
- Principio de mínimo privilegio.
- Archivos clínicos privados y servidos solo tras autorización.
- URLs públicas predecibles no conceden acceso a documentos clínicos.
- Credenciales y secretos solo en variables de entorno/secret manager, nunca Git.
- Información sensible excluida de logs y mensajes de error.
- Auditoría para consultas/modificaciones críticas según la política que se concrete por recurso.

## Autenticación web

- Autenticación basada en sesión con mecanismos nativos de Laravel.
- Regeneración del identificador de sesión después de un login exitoso.
- Invalidación de la sesión y regeneración del token CSRF al cerrar sesión.
- Errores de credenciales genéricos: no deben revelar si una cuenta existe.
- Rutas para invitados y usuarios autenticados protegidas con middleware `guest` y `auth`.
- No se implementan roles/permisos dentro de URPE-3; autorización granular es una tarea posterior.
- Antes de producción se definirá rate limiting específico para autenticación y políticas finales de contraseña/recuperación.

## Acceso por perfil

- Administrador: gestión global sujeta a auditoría.
- Coordinación clínica: acceso clínico amplio según permisos.
- Terapeuta: acceso limitado a pacientes/casos autorizados; regla exacta pendiente de cerrar.
- Recepción: acceso operativo/administrativo; acceso clínico mínimo por definir.
- Consulta/Dirección: lectura restringida según permisos.

## Datos clínicos

- No se borran silenciosamente.
- Correcciones relevantes deben conservar trazabilidad.
- Adjuntos se almacenan fuera del directorio público.
- Descargas requieren autorización en el momento de acceso.

## Antes de producción

- HTTPS obligatorio.
- Backups cifrados o protegidos según infraestructura.
- Política de retención y restauración probada.
- Revisión de permisos.
- Revisión de configuración APP_DEBUG/APP_ENV.
- Rate limiting donde aplique.
- Cabeceras de seguridad y hardening de servidor.
- Revisión legal/operativa de tratamiento y conservación de datos por parte del responsable del servicio.
