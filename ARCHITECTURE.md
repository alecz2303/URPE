# Architecture — URPE Gestión Clínica

## Stack base

- PHP 8.4
- Laravel 13
- Blade/Vite para la base web; decisiones de componentes interactivos se tomarán por tarea.
- MySQL como motor relacional para desarrollo local y producción.
- SQLite `:memory:` para la suite automatizada por defecto, salvo pruebas que requieran comportamiento específico de MySQL.
- PHPUnit para pruebas automatizadas.

## Estilo

Aplicación monolítica modular Laravel para V1. Se prioriza simplicidad, seguridad y capacidad de evolución sobre microservicios prematuros.

## Dominios previstos

- Identity & Access
- Configuration
- Therapists
- Therapies
- Patients
- Scheduling
- Clinical Records
- Session Notes
- Documents
- Audit
- Reporting

## Capas

- HTTP: rutas, requests, controllers/middleware.
- Application: acciones/casos de uso y coordinación transaccional.
- Domain: reglas y modelos de negocio donde aporten claridad.
- Infrastructure: Eloquent, almacenamiento, colas/integraciones futuras.

## Identity & Access

URPE-3 establece autenticación web basada en sesión usando mecanismos nativos de Laravel. La autenticación y la autorización se mantienen separadas: URPE-3 resuelve identidad/login; roles y permisos granulares se implementarán en una tarea posterior.

## Principios

- Controllers delgados.
- Validación mediante Form Requests cuando aplique.
- Policies/Gates/permisos para autorización backend.
- Transacciones para operaciones clínicas/agenda que afecten varios recursos.
- Reglas de colisión y disponibilidad centralizadas y testeables.
- Configuración operativa persistida como datos.
- Archivos clínicos fuera de acceso público directo.
- Auditoría desacoplada del contenido sensible.

## Agenda

Scheduling será un dominio central. La disponibilidad se evalúa por intervalo completo y por todos los terapeutas requeridos. Las citas existentes conservan su información histórica aunque cambie configuración futura; la política exacta se cerrará en la tarea correspondiente.
