# Database — Diseño inicial conceptual

Este documento define entidades, no migraciones finales. Cada tarea aprobará su esquema concreto mediante migraciones.

## Motor canónico

URPE utilizará **MySQL** como motor para desarrollo local y producción.

Baseline local con Laragon:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=urpe
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

Las credenciales reales pertenecen exclusivamente al `.env` local/servidor y nunca se versionan.

La suite automática usa SQLite `:memory:` por defecto mediante `phpunit.xml`, salvo pruebas de integración que requieran validar comportamiento específico de MySQL.

## Entidades previstas

### Acceso
- users
- roles / permissions (implementación por definir)
- audit_events

### Configuración
- settings / operating_hours

### Clínica
- therapists
- therapist_availabilities
- therapist_blocks
- therapy_types
- patients
- patient_guardians
- clinical_histories / clinical_entries
- clinical_documents

### Agenda
- appointments
- appointment_therapists
- appointment_status_history
- recurrence metadata/series según diseño aprobado

### Evolución
- session_notes
- session_note_therapists si se requiere relación múltiple
- session_note_attachments

## Reglas de persistencia

- IDs no deben exponerse como mecanismo de autorización.
- Fechas clínicas y de auditoría conservan precisión suficiente para trazabilidad.
- Relaciones sensibles usan integridad referencial.
- La eliminación física de información clínica no será comportamiento por defecto.
- Los cambios de esquema solo entran mediante migraciones versionadas.
- Índices deben cubrir búsquedas por paciente, terapeuta, intervalos de agenda y auditoría según uso real.
