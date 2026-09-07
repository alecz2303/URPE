# TODO — URPE Gestión Clínica

## URPE-1 — Fundación canónica

- [x] Definir repositorio oficial.
- [x] Establecer `master` como rama estable.
- [x] Proteger `master`: PR obligatorio + mínimo 1 aprobación.
- [x] Definir Laravel 13 / PHP 8.4 como stack base.
- [x] Crear documentación canónica inicial.
- [x] Crear skeleton base reproducible.
- [x] Revisión y aprobación humana del paquete URPE-1.
- [x] Merge de URPE-1 a `master` vía PR.
- [x] Crear tareas Jira derivadas del Roadmap.

## URPE-2 — Bootstrap técnico

- [x] Normalizar skeleton Laravel 13 / PHP 8.4.
- [x] Completar configuración Laravel y runtime paths.
- [x] Definir baseline local MySQL y testing SQLite en memoria.
- [x] Incorporar `composer.lock` y `package-lock.json`.
- [x] Configurar GitHub Actions para tests y build.
- [x] Validar Composer, suite PHP y build frontend.
- [x] Squash + rebase/sincronización contra `master`.
- [x] PR aprobado y rebase merge a `master`.
- [x] CI post-merge en `master` verde.

## URPE-3 — Autenticación — CERRADO

- [x] Login seguro por sesión.
- [x] Logout con invalidación/regeneración de sesión.
- [x] Rutas `guest` y `auth` protegidas.
- [x] Mensajes genéricos para credenciales inválidas.
- [x] Vistas de autenticación integradas con URPE.
- [x] Tests de login, credenciales inválidas, logout y protección de rutas.
- [x] Regresión completa y build frontend verdes en CI de rama.
- [x] Actualizar documentación afectada.
- [x] Validación local en Laragon: login, dashboard y estilos confirmados visualmente.
- [x] Squash + rebase/sincronización contra `master` antes de PR.
- [x] PR #3 aprobado, rebase merge y Test #13 post-merge en verde.

## URPE-4 — Usuarios, roles y permisos — CERRADO

- [x] Definir modelo de usuarios, roles y permisos granulares.
- [x] Crear roles iniciales: Administrador, Coordinación Clínica, Terapeuta, Recepción y Consulta/Dirección.
- [x] Establecer autorización granular mediante Laravel Gate, sin condicionales dispersos por nombre de rol.
- [x] Implementar relaciones y asignación/sincronización de roles y permisos.
- [x] Proteger rutas/acciones administrativas con autorización granular.
- [x] Implementar administración básica de usuarios: listado, alta, edición y asignación de rol.
- [x] Separar permisos de gestión de cuentas (`users.*`) de la asignación de roles (`roles.manage`).
- [x] Implementar administración básica de roles y permisos.
- [x] Implementar activación/desactivación segura de cuentas y bloqueo de login para cuentas inactivas.
- [x] Invalidar la sesión de una cuenta desactivada en su siguiente solicitud autenticada.
- [x] Incorporar `urpe:grant-admin` para bootstrap reproducible del primer administrador.
- [x] Cubrir accesos permitidos/denegados, asignación de roles, permisos, estado de cuenta y bootstrap de administrador con tests.
- [x] Suite local completa verde: 41/41 tests.
- [x] Validación visual final en Laragon de Usuarios + Roles y permisos.
- [x] Squash + rebase contra `master` antes de PR.
- [x] PR #4 aprobado, rebase merge y Test #68 post-merge en verde.

## URPE-5 — Auditoría y archivos clínicos — CERRADO

- [x] Implementar modelo y persistencia reutilizable de auditoría.
- [x] Registrar autenticación y acciones administrativas sensibles.
- [x] Excluir contraseñas, tokens, secretos y credenciales de metadata de auditoría.
- [x] Crear almacenamiento clínico privado fuera de `public/`.
- [x] Implementar metadatos, UUID, SHA-256 y relación futura con entidades clínicas.
- [x] Implementar descarga protegida por permiso granular.
- [x] Implementar retiro lógico conservando archivo físico y trazabilidad.
- [x] Incorporar baseline reutilizable de SweetAlert para feedback y confirmaciones sensibles.
- [x] Validar suite completa y build frontend.
- [x] Actualizar documentación técnica final de URPE-5.
- [x] Validación visual final en Laragon.
- [x] Squash + rebase contra `master` antes de PR.
- [x] PR #5 aprobado, rebase merge, CI post-merge Test #102 verde y cierre Jira.

## URPE-6 — Configuración del centro y horarios — CERRADO

- [x] Crear persistencia de configuración general del centro.
- [x] Crear persistencia de horarios operativos semanales.
- [x] Definir horario inicial editable 09:00–18:00.
- [x] Permitir días habilitados/deshabilitados y múltiples ventanas por día.
- [x] Validar rangos inválidos y traslapes.
- [x] Normalizar horarios a `HH:MM:SS` de forma consistente entre SQLite/MySQL.
- [x] Implementar servicio reutilizable para consultar si el centro está abierto en un instante.
- [x] Implementar validación reutilizable de intervalos completos para la futura agenda.
- [x] Proteger configuración con permiso granular `center.manage`.
- [x] Auditar cambios mediante `center.configuration_updated`.
- [x] Implementar interfaz administrativa de datos generales y horarios.
- [x] Agregar soporte visual para múltiples ventanas por día.
- [x] Integrar SweetAlert para confirmación, éxito y errores.
- [x] Integrar acceso condicionado desde dashboard.
- [x] Validación funcional local de interfaz y permisos.
- [x] Validar suite completa y build frontend final.
- [x] Squash + rebase/sincronización contra `master`.
- [x] PR #6 aprobado, rebase merge, CI post-merge y cierre Jira.

## URPE-7 — Terapeutas y disponibilidad — CERRADO

- [x] Crear perfiles operativos de terapeutas vinculables a usuarios internos.
- [x] Sincronizar automáticamente usuarios con rol Terapeuta hacia su perfil operativo.
- [x] Incorporar estado activo/inactivo y datos profesionales del terapeuta.
- [x] Crear disponibilidad semanal configurable con múltiples ventanas por día.
- [x] Subordinar la disponibilidad individual al horario operativo del centro.
- [x] Rechazar rangos inválidos y ventanas traslapadas.
- [x] Crear ausencias/bloqueos como restricciones de disponibilidad.
- [x] Implementar validación reutilizable de disponibilidad durante el intervalo completo.
- [x] Proteger administración mediante permiso granular `therapists.manage`.
- [x] Implementar interfaz de listado, alta, edición, disponibilidad y bloqueos.
- [x] Integrar SweetAlert para feedback de operaciones.
- [x] Auditar cambios de perfil, disponibilidad y bloqueos.
- [x] Validar acceso permitido/denegado mediante pruebas funcionales.
- [x] Validar reglas de disponibilidad mediante pruebas de dominio.
- [x] Validación visual en Laragon, incluido usuario Terapeuta existente sincronizado.
- [x] Regresión local completa: 87 pruebas / 294 assertions verdes.
- [x] CI de rama verde.
- [x] Build frontend final verde.
- [x] Squash + rebase/sincronización contra `master`.
- [x] PR #7 aprobado, rebase merge, CI post-merge y cierre Jira.

## URPE-8 — Catálogo configurable de terapias — CERRADO

- [x] Crear persistencia y modelo `Therapy`.
- [x] Incorporar nombre, duración, terapeutas requeridos, color y estado activo/inactivo.
- [x] Sembrar Vojta 40 min / 1 terapeuta y Pediasuit 60 min / 2 terapeutas como datos configurables.
- [x] Permitir alta y edición de terapias sin cambios de código.
- [x] Validar duración, cantidad de terapeutas, nombre único y color hexadecimal.
- [x] Mantener terapias inactivas para integridad histórica, sin eliminación destructiva.
- [x] Proteger administración con `therapies.manage`.
- [x] Otorgar `therapies.manage` a Administrador y Coordinación Clínica; no otorgarlo al rol Terapeuta por defecto.
- [x] Implementar listado, alta, edición y activación/desactivación.
- [x] Integrar acceso desde dashboard.
- [x] Integrar SweetAlert en cambios de estado y feedback.
- [x] Auditar creación, actualización y cambio de estado.
- [x] Agregar pruebas positivas/negativas de permisos, persistencia, validación, seed, auditoría y estado.
- [x] Validación funcional local en Laragon confirmada.
- [x] Suite completa y build frontend confirmados por validación local.
- [x] CI de rama verde antes del cierre técnico.
- [x] Actualizar documentación canónica de URPE-7/URPE-8.
- [x] Squash + rebase/sincronización contra `master` antes de PR.
- [x] PR #8 aprobado, rebase merge, CI post-merge y cierre Jira.

## URPE-9 — Pacientes y responsables — CERRADO

- [x] Crear persistencia y modelos de pacientes, responsables y vínculo paciente-responsable.
- [x] Implementar responsable principal único por paciente.
- [x] Implementar folio anual consecutivo `URPE-AAAA-NNNNNN`.
- [x] Proteger acceso con `patients.view` y `patients.manage`.
- [x] Mantener al rol Terapeuta sin acceso global a pacientes por defecto.
- [x] Implementar listado, alta, ficha, edición y activación/desactivación de pacientes.
- [x] Implementar alta, edición y cambio de responsable principal.
- [x] Integrar SweetAlert en acciones sensibles.
- [x] Auditar cambios administrativos sin duplicar payloads sensibles completos.
- [x] Agregar cobertura de dominio, autorización, validación, auditoría y folios.
- [x] Validación funcional local en Laragon.
- [x] Suite completa y CI de rama verdes.
- [x] Squash + sincronización contra `master`.
- [x] PR #9 aprobado, rebase merge, CI post-merge #216 verde y cierre Jira.

## URPE-10 — Expediente clínico base — EN CURSO

- [x] Crear persistencia uno-a-uno entre paciente y expediente clínico base.
- [x] Incorporar antecedentes médicos, prenatales/perinatales, del desarrollo y familiares.
- [x] Incorporar diagnósticos, objetivos terapéuticos y observaciones clínicas generales.
- [x] Mantener el expediente clínico separado de datos y notas administrativas.
- [x] Mantener evolución por sesión fuera del alcance del expediente base.
- [x] Impedir eliminación destructiva del paciente mientras exista expediente clínico.
- [x] Proteger lectura y administración con `clinical_records.view` y `clinical_records.manage`.
- [x] Otorgar acceso base a Administrador y Coordinación Clínica sin acceso clínico global automático para Terapeuta.
- [x] Implementar pantalla de expediente clínico desde el contexto del paciente.
- [x] Integrar acceso condicionado al expediente desde la ficha administrativa del paciente.
- [x] Integrar SweetAlert para feedback de creación y actualización.
- [x] Auditar `clinical_record.created` y `clinical_record.updated` sin duplicar texto clínico sensible en metadata.
- [x] Agregar pruebas de persistencia, unicidad, validación, autorización, auditoría, SweetAlert y acceso desde ficha del paciente.
- [x] Validación funcional local de creación, persistencia, actualización y acceso desde ficha confirmada.
- [x] Actualizar ROADMAP, BUSINESS_RULES, CHANGELOG y TODO.
- [ ] Confirmar CI final de rama verde después del Bloque 3.
- [ ] Squash de todos los commits de URPE-10 en un único commit.
- [ ] Rebase/sincronización contra `master` antes de PR.
- [ ] CI verde sobre el commit final único.
- [ ] PR aprobado, rebase merge, CI post-merge, limpieza de rama y cierre Jira.

## V1 — Backlog canónico

- [x] Autenticación y gestión de usuarios.
- [x] Roles y permisos granulares.
- [x] Auditoría base de acciones sensibles.
- [x] Configuración general y horarios operativos. *(URPE-6 cerrado)*
- [x] Catálogo de terapeutas y disponibilidad. *(URPE-7 cerrado)*
- [x] Catálogo de terapias configurable. *(URPE-8 cerrado)*
- [x] Pacientes y tutores/responsables. *(URPE-9 cerrado)*
- [ ] Agenda/calendario clínico.
- [ ] Reglas de disponibilidad y colisiones.
- [ ] Citas recurrentes, reprogramación y cancelación.
- [x] Expediente clínico digital base. *(URPE-10 implementado; cierre técnico en curso)*
- [ ] Documentos, imágenes, radiografías y estudios vinculados al expediente.
- [ ] Bitácoras/evolución de sesiones.
- [ ] Dashboard operativo.
- [ ] Reportes operativos esenciales.
- [ ] Hardening, pruebas de regresión y preparación de despliegue.

## Fuera de V1

- WhatsApp y automatización de mensajes.
- Integraciones externas no aprobadas.
- Facturación/cobranza salvo aprobación explícita posterior.
