# Database — Diseño inicial conceptual

Este documento define entidades y registra los esquemas ya aprobados por tareas cerradas o en revisión. Cada cambio de esquema entra mediante migraciones versionadas.

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
- roles / permissions
- audit_events

### Configuración
- settings / operating_hours

### Clínica
- therapists
- therapist_availabilities
- therapist_blocks
- therapies
- patients
- patient_guardians
- clinical_records
- clinical_documents

### Agenda — baseline URPE-11 + recurrencia URPE-14

#### `appointments`
- `id`
- `patient_id` FK restrict
- `therapy_id` FK restrict
- `appointment_series_id` nullable FK hacia `appointment_series`
- `series_occurrence` nullable
- `starts_at`
- `ends_at`
- `duration_minutes`
- `status` (`scheduled` / `cancelled` en baseline)
- `cancellation_reason` nullable
- `cancelled_at` nullable
- timestamps
- índices por tiempos, estado y relación de serie

#### `appointment_therapist`
- `appointment_id` FK cascade
- `therapist_id` FK restrict
- timestamps
- PK compuesta `appointment_id + therapist_id`
- índice por terapeuta

#### `appointment_series`
- `id`
- `patient_id` FK restrict
- `therapy_id` FK restrict
- `starts_at_time`
- `starts_on`
- `ends_on`
- `weekdays` JSON/array serializada mediante cast del modelo
- timestamps

La duración queda persistida como snapshot operativo de la terapia usada al crear/reprogramar la cita. La relación con terapeutas es muchos-a-muchos para soportar terapias con uno o varios recursos humanos requeridos.

Una serie recurrente no sustituye las citas individuales: cada ocurrencia se materializa como un registro normal en `appointments`, vinculado opcionalmente a `appointment_series` y numerado mediante `series_occurrence`. Esto permite auditoría, cancelación y reprogramación granular sin crear una cita infinita o virtual.

URPE-14 mantiene la recurrencia semanal acotada por `starts_on` y `ends_on`. Las operaciones masivas sobre una serie validan todas las citas afectadas antes de persistir cambios para evitar estados parciales.

Pendiente de fases posteriores:
- historial de estados ampliado
- no-show y finalización operativa
- filtros y estados adicionales.

### Evolución
- session_notes
- session_note_therapists si se requiere relación múltiple
- session_note_attachments

## Reglas de persistencia

- IDs no deben exponerse como mecanismo de autorización.
- Fechas clínicas y de auditoría conservan precisión suficiente para trazabilidad.
- Relaciones sensibles usan integridad referencial.
- La eliminación física de información clínica no será comportamiento por defecto.
- Las citas se cancelan por estado; no se eliminan físicamente como flujo normal.
- Las series recurrentes son contenedores operativos; las ocurrencias siguen siendo citas individuales auditables.
- Los cambios de esquema solo entran mediante migraciones versionadas.
- Índices deben cubrir búsquedas por paciente, terapeuta, intervalos de agenda, series recurrentes y auditoría según uso real.
