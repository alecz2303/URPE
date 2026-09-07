# Changelog

Todos los cambios relevantes de URPE Gestión Clínica se documentarán aquí.

## [Unreleased]

### Added
- Repositorio oficial y rama estable `master`.
- Protección de `master` mediante PR y 1 aprobación mínima.
- Base técnica Laravel 13 / PHP 8.4 / MySQL.
- Documentación canónica inicial.
- Scope V1 y exclusión explícita de WhatsApp.
- Reglas de negocio iniciales y decisiones abiertas.
- Arquitectura, seguridad y modelo conceptual de datos.
- Bootstrap técnico completo de Laravel 13.
- Configuración Laravel y runtime paths reproducibles.
- `composer.lock` y `package-lock.json` versionados.
- GitHub Actions para pruebas PHP y build frontend.
- Autenticación segura por sesión con login, logout y protección de rutas.
- Interfaz base de login/dashboard con Tailwind 4 + Vite correctamente integrado.
- Pruebas de autenticación y regresión para acceso invitado/autenticado.
- Modelo RBAC de primera parte con usuarios, roles, permisos y tablas pivote.
- Roles iniciales: Administrador, Coordinación Clínica, Terapeuta, Recepción y Consulta/Dirección.
- Permisos administrativos granulares para usuarios y roles.
- Administración básica de usuarios con alta, edición y estado activo/inactivo.
- Administración básica de roles y asignación de permisos.
- Separación entre permisos de gestión de cuentas y permisos de asignación de roles.
- Invalidación de sesiones de cuentas desactivadas.
- Comando `urpe:grant-admin` para bootstrap reproducible del primer administrador.
- Cobertura automatizada de autorización positiva/negativa, cambios de rol, estado de cuenta y bootstrap administrativo.
- Auditoría reutilizable de acciones sensibles y almacenamiento clínico privado con descarga protegida.
- Baseline reutilizable de SweetAlert para confirmaciones y feedback.
- Configuración general del centro con datos de identidad/contacto y zona horaria.
- Horarios operativos semanales configurables, días habilitados/deshabilitados y múltiples ventanas por día.
- Permiso granular `center.manage` y auditoría `center.configuration_updated`.
- Interfaz administrativa para editar configuración y horarios del centro.
- Validación reutilizable de apertura puntual y de intervalos completos para la futura agenda.
- Perfiles operativos de terapeutas vinculables a cuentas internas y sincronización de usuarios con rol Terapeuta.
- Disponibilidad semanal individual con múltiples ventanas por día subordinadas al horario del centro.
- Ausencias/bloqueos de terapeutas como restricciones explícitas de disponibilidad.
- Servicio reutilizable para validar disponibilidad del terapeuta durante el intervalo completo de una futura cita.
- Permiso granular `therapists.manage` y administración de perfiles, disponibilidad y bloqueos.
- Interfaz administrativa de terapeutas integrada al dashboard y feedback mediante SweetAlert.
- Cobertura automatizada de dominio, autorización, persistencia, auditoría, horarios, bloqueos e intervalos completos para terapeutas.
- Integración del logotipo oficial URPE en login y dashboard.
- Catálogo configurable de terapias con nombre, duración, cantidad requerida de terapeutas, color y estado activo/inactivo.
- Seed inicial de Vojta (40 min / 1 terapeuta) y Pediasuit (60 min / 2 terapeutas) como datos configurables.
- Permiso granular `therapies.manage`, asignado en el baseline a Administrador y Coordinación Clínica.
- Interfaz de terapias con listado, alta, edición y activación/desactivación sin eliminación destructiva.
- SweetAlert para cambios de estado y feedback de administración de terapias.
- Auditoría de `therapy.created`, `therapy.updated` y `therapy.status_updated`.
- Cobertura automatizada de catálogo, permisos, seed, validaciones, auditoría, estado activo/inactivo y visibilidad desde dashboard.
- Dominio administrativo de pacientes y tutores/responsables con relación muchos-a-muchos, parentesco y responsable principal único por paciente.
- Folios de paciente consecutivos por año con formato `URPE-AAAA-NNNNNN` y secuencia transaccional dedicada.
- Permisos granulares `patients.view` y `patients.manage`, con acceso administrativo base para Administrador, Coordinación Clínica y Recepción, lectura para Consulta/Dirección y sin acceso global para Terapeuta.
- Interfaz de pacientes con listado, alta, ficha, edición, activación/desactivación y gestión de responsables.
- Auditoría de creación, actualización, estado, responsables y cambio de responsable principal.
- SweetAlert para cambios sensibles de pacientes y responsables, y formularios visualmente normalizados.
- Cobertura automatizada de dominio, permisos, validación, relaciones, responsable principal, estado, auditoría y folios anuales consecutivos.
- Expediente clínico base con relación uno-a-uno por paciente y secciones estructuradas para antecedentes, diagnósticos, objetivos terapéuticos y observaciones clínicas generales.
- Permisos granulares `clinical_records.view` y `clinical_records.manage`, con acceso base para Administrador y Coordinación Clínica y sin acceso clínico global automático para Terapeuta.
- Interfaz de expediente clínico accesible desde la ficha del paciente solo para usuarios autorizados, sin exponer contenido clínico en la ficha administrativa.
- Creación y actualización del expediente clínico con auditoría `clinical_record.created` y `clinical_record.updated` sin duplicar textos clínicos sensibles en metadata.
- Integración de SweetAlert para feedback del expediente clínico.
- Cobertura automatizada de persistencia uno-a-uno, autorización positiva/negativa, validación, auditoría, UI y acceso condicionado desde la ficha del paciente.

### Changed
- URPE-1 cerrado e integrado en `master`.
- URPE-2 cerrado e integrado mediante squash + rebase/rebase merge.
- URPE-3 cerrado mediante squash + rebase/rebase merge y validado en `master` con Test #13 verde.
- URPE-4 cerrado mediante PR #4 y rebase merge.
- URPE-5 cerrado mediante PR #5, rebase merge y Test #102 post-merge verde.
- URPE-6 cerrado mediante PR #6, rebase merge, CI post-merge y cierre Jira.
- URPE-7 cerrado mediante PR #7, rebase merge y CI post-merge #161 verde.
- URPE-8 cerrado mediante PR #8, rebase merge y CI post-merge #184 verde.
- URPE-9 cerrado mediante PR #9, rebase merge y CI post-merge #216 verde.
- URPE-10 implementado funcionalmente y validado en Laragon; pendiente squash/rebase, PR, aprobación, rebase merge, CI post-merge y cierre Jira.
