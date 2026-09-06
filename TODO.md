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

## URPE-6 — Configuración del centro y horarios — EN CURSO

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
- [x] Suite local Bloque 2: 68/68 pruebas verdes.
- [x] GitHub Actions Test #117 verde.
- [x] Actualizar documentación canónica de URPE-5/URPE-6.
- [ ] Validar suite completa después del hardening de intervalos.
- [ ] Validar build frontend final.
- [ ] Squash + rebase/sincronización contra `master` antes de PR.
- [ ] PR aprobado, rebase merge, CI post-merge, limpieza de ramas y cierre Jira.

## V1 — Backlog canónico

- [x] Autenticación y gestión de usuarios.
- [x] Roles y permisos granulares.
- [x] Auditoría base de acciones sensibles.
- [ ] Configuración general y horarios operativos. *(URPE-6 en curso)*
- [ ] Catálogo de terapeutas y disponibilidad.
- [ ] Catálogo de terapias configurable.
- [ ] Pacientes y tutores/responsables.
- [ ] Agenda/calendario clínico.
- [ ] Reglas de disponibilidad y colisiones.
- [ ] Citas recurrentes, reprogramación y cancelación.
- [ ] Expediente clínico digital.
- [ ] Documentos, imágenes, radiografías y estudios.
- [ ] Bitácoras/evolución de sesiones.
- [ ] Dashboard operativo.
- [ ] Reportes operativos esenciales.
- [ ] Hardening, pruebas de regresión y preparación de despliegue.

## Fuera de V1

- WhatsApp y automatización de mensajes.
- Integraciones externas no aprobadas.
- Facturación/cobranza salvo aprobación explícita posterior.
