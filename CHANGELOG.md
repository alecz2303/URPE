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

### Changed
- URPE-1 cerrado e integrado en `master`.
- URPE-2 cerrado e integrado mediante squash + rebase/rebase merge.
- URPE-3 cerrado mediante squash + rebase/rebase merge y validado en `master` con Test #13 verde.
- `master` estable después de URPE-3: `588250eb0835a176c85bf8d6a8c4256ebe08fb34`.
- URPE-4 avanzó a validación final con 41/41 pruebas locales verdes y GitHub Actions Test #60 verde.
