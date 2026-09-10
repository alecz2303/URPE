# Security — URPE Gestión Clínica

URPE procesará información clínica y datos personales; seguridad es requisito transversal.

## Baseline

- Contraseñas mediante hashing provisto por Laravel.
- CSRF activo en formularios web y solicitudes de autoguardado clínico.
- Sesiones seguras y cookies adecuadas al ambiente.
- Validación server-side.
- Autorización server-side en cada operación sensible, incluido autoguardado; la visibilidad de UI nunca sustituye esta validación.
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
- Coordinación clínica: acceso clínico amplio según permisos, incluido `session_logs.manage_all`.
- Terapeuta: acceso a bitácoras únicamente cuando su cuenta se resuelve al perfil profesional vinculado y existe asignación válida a la cita o participación histórica válida en esa sesión.
- El rol Terapeuta por sí solo no concede acceso clínico global a pacientes ni permite autoasignarse a citas.
- La consulta histórica puede mantenerse para un terapeuta que quedó persistido como participante clínico de una sesión; ese vínculo histórico no concede por sí solo permiso para agregar enmiendas después de una sustitución.
- Para agregar una enmienda, un terapeuta sin `session_logs.manage_all` debe conservar `session_logs.manage` y una asignación vigente a la cita.
- Una sustitución de terapeuta debe ser ejecutada por un usuario con permiso de gestión de agenda; la autorización clínica se actualiza según la asignación efectiva y el cambio queda auditado.
- El listado global de Sesiones clínicas aplica el mismo alcance: `session_logs.manage_all` permite alcance global; de lo contrario solo se incluyen sesiones con asignación actual o participación histórica válida.
- Recepción: acceso operativo/administrativo; no recibe acceso a bitácoras clínicas por defecto.
- Consulta/Dirección: lectura restringida según permisos; no recibe acceso a bitácoras clínicas por defecto.

## Datos clínicos

- No se borran silenciosamente.
- Correcciones relevantes deben conservar trazabilidad.
- La bitácora clínica se almacena separada del expediente clínico base y de los metadatos de agenda.
- El contenido clínico completo de una bitácora o de una enmienda no se replica en eventos de auditoría; se registran identificadores, estado y contexto mínimo necesario para trazabilidad.
- El autoguardado de una sesión reutiliza el único borrador canónico de la cita, valida nuevamente permisos y participantes en servidor y no crea una nueva bitácora por cada cambio.
- El primer autoguardado que materializa el borrador registra creación auditada sin incluir los textos clínicos; autoguardados posteriores no generan eventos repetitivos que dupliquen actividad clínica en metadata.
- Una bitácora completada queda cerrada para edición. URPE-17 no la reabre: toda corrección o complemento posterior se agrega como una enmienda clínica independiente.
- El endpoint de autoguardado también rechaza bitácoras completadas y citas canceladas, por lo que JavaScript no puede reabrir ni sobrescribir una sesión cerrada.
- Cada enmienda conserva vínculo con la bitácora, autor, motivo, contenido y timestamp, y no dispone de eliminación destructiva dentro del flujo clínico normal.
- La nota original y sus enmiendas se muestran como registros diferenciados para impedir que una corrección posterior parezca haber formado parte del texto original.
- Los terapeutas participantes se persisten en la sesión para que cambios posteriores de agenda no reescriban quién atendió realmente al paciente.
- El historial de sustituciones conserva terapeuta removido, terapeuta agregado, actor, momento y motivo operativo opcional.
- La línea longitudinal del paciente reutiliza las mismas reglas de autorización de bitácoras y no crea una vía alternativa para ampliar acceso clínico.
- El contexto longitudinal mostrado durante la captura reutiliza únicamente sesiones completadas previas y se presenta como solo lectura; no ofrece una vía de edición histórica.
- Las migraciones de backfill de permisos clínicos son aditivas: garantizan los grants baseline necesarios para roles del sistema sin eliminar permisos personalizados ya existentes.

## Archivos clínicos protegidos

- Los adjuntos se almacenan fuera del directorio público mediante el disco `clinical`; el nombre físico es generado por el sistema y no depende del nombre original proporcionado por el usuario.
- Se conserva hash SHA-256, MIME, extensión, tamaño y nombre original como metadata técnica para trazabilidad; el binario no se persiste en la base de datos.
- URPE-22 vincula los archivos longitudinales al `ClinicalRecord` mediante la relación polimórfica `subject_type/subject_id`, reutilizando `ClinicalFileStorage` y evitando almacenes paralelos.
- Carga y retiro desde el expediente requieren `clinical_records.manage` en servidor. Mostrar u ocultar botones en Blade no sustituye esta autorización.
- La descarga de un archivo cuyo subject es `ClinicalRecord` requiere `clinical_records.view` en el momento de la solicitud. Conocer el UUID del archivo no concede acceso.
- Archivos clínicos legacy sin subject de expediente conservan el permiso específico `clinical_files.download` para compatibilidad con la infraestructura de URPE-5.
- El retiro normal de un archivo es lógico: se aplica soft delete al registro activo, se conservan bytes físicos y se registra `clinical_file.retired` para trazabilidad. No existe eliminación destructiva desde el flujo clínico ordinario.
- Un archivo sólo puede retirarse desde el mismo paciente/expediente al que está vinculado; el backend valida explícitamente `subject_type` y `subject_id` para impedir operaciones cruzadas entre pacientes.
- La auditoría de carga, descarga y retiro registra identificadores, hash y contexto técnico mínimo, pero no copia el contenido del archivo ni descripciones clínicas completas innecesarias.
- La clasificación operativa de archivo (`document`, `image`, `radiograph`, `study`, `other`) y la descripción opcional se conservan en metadata; esa metadata no debe utilizarse para almacenar notas clínicas extensas.
- El baseline V1 permite PDF, JPG, JPEG, PNG y WEBP, con límite de 20 MB por archivo validado en servidor.

## Antes de producción

- HTTPS obligatorio.
- Backups cifrados o protegidos según infraestructura.
- Política de retención y restauración probada.
- Revisión de permisos.
- Revisión de configuración APP_DEBUG/APP_ENV.
- Rate limiting donde aplique.
- Cabeceras de seguridad y hardening de servidor.
- Revisión legal/operativa de tratamiento y conservación de datos por parte del responsable del servicio.
