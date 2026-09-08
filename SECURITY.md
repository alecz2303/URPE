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
- La autorización granular se resuelve con permisos de primera parte y no mediante condicionales dispersos por nombre de rol.
- Crear un terapeuta aprovisiona de forma atómica su cuenta interna, asigna el rol Terapeuta y conserva `therapists.user_id` como vínculo técnico de identidad.
- La contraseña inicial del terapeuta es temporal, se genera de forma segura y se muestra únicamente en el flujo de alta; no se persiste en auditoría ni en metadata clínica.
- Desactivar un terapeuta también desactiva su cuenta de acceso sin eliminar el usuario, el perfil profesional ni la historia clínica relacionada.
- Antes de producción se definirá rate limiting específico para autenticación y políticas finales de contraseña/recuperación, incluido el flujo definitivo de cambio obligatorio de credencial inicial.

## Acceso por perfil

- Administrador: gestión global sujeta a auditoría.
- Coordinación clínica: acceso clínico amplio según permisos, incluido `session_logs.manage_all` en el baseline de URPE-16.
- Terapeuta: acceso a bitácoras únicamente cuando su cuenta se resuelve al perfil profesional vinculado y existe asignación válida a la cita o participación histórica válida en esa sesión.
- El rol Terapeuta por sí solo no concede acceso clínico global a pacientes ni permite autoasignarse a citas.
- Una sustitución de terapeuta debe ser ejecutada por un usuario con permiso de gestión de agenda; la autorización clínica se actualiza según la asignación efectiva y el cambio queda auditado.
- Recepción: acceso operativo/administrativo; no recibe acceso a bitácoras clínicas por defecto.
- Consulta/Dirección: lectura restringida según permisos; no recibe acceso a bitácoras clínicas por defecto en URPE-16.

## Datos clínicos

- No se borran silenciosamente.
- Correcciones relevantes deben conservar trazabilidad.
- La bitácora clínica se almacena separada del expediente clínico base y de los metadatos de agenda.
- El contenido clínico completo de una bitácora no se replica en eventos de auditoría; se registran identificadores, estado, participantes y timestamps necesarios para trazabilidad.
- Una bitácora completada queda cerrada para edición dentro del baseline de URPE-16.
- Los terapeutas participantes se persisten en la sesión para que cambios posteriores de agenda no reescriban quién atendió realmente al paciente.
- El historial de sustituciones conserva terapeuta removido, terapeuta agregado, actor, momento y motivo operativo opcional.
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
