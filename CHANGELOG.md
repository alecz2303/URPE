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

### Changed
- URPE-1 cerrado e integrado en `master`.
- URPE-2 cerrado e integrado mediante squash + rebase/rebase merge.
- `master` validado después de URPE-2 con GitHub Actions en verde.
- URPE-3 iniciado para implementar autenticación segura por sesión.
