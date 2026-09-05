# Database — Diseño inicial conceptual

Este documento define entidades, no migraciones finales. Cada tarea aprobará su esquema concreto mediante migraciones.

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

## Motor

El motor de producción se definirá antes de la primera migración de dominio para evitar asumir infraestructura no confirmada.
