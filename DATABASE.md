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
- clinical_session_logs
- clinical_session_log_therapist

### Agenda — baseline URPE-11 + recurrencia URPE-14 + sustituciones URPE-16

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

#### `appointment_therapist_changes`
- `id`
- `appointment_id` FK restrict
- `removed_therapist_id` nullable FK hacia `therapists`
- `added_therapist_id` nullable FK hacia `therapists`
- `changed_by_user_id` nullable FK hacia `users`
- `reason` nullable
- timestamps
- índice por cita + fecha de cambio

La duración queda persistida como snapshot operativo de la terapia usada al crear/reprogramar la cita. La relación con terapeutas es muchos-a-muchos para soportar terapias con uno o varios recursos humanos requeridos.

Una serie recurrente no sustituye las citas individuales: cada ocurrencia se materializa como un registro normal en `appointments`, vinculado opcionalmente a `appointment_series` y numerado mediante `series_occurrence`. Esto permite auditoría, cancelación y reprogramación granular sin crear una cita infinita o virtual.

URPE-14 mantiene la recurrencia semanal acotada por `starts_on` y `ends_on`. Las operaciones masivas sobre una serie validan todas las citas afectadas antes de persistir cambios para evitar estados parciales.

URPE-16 agrega sustituciones operativas de terapeuta sin reemplazar destructivamente el contexto histórico: la asignación efectiva vive en `appointment_therapist` y cada cambio queda registrado en `appointment_therapist_changes`.

Pendiente de fases posteriores:
- historial de estados ampliado
- no-show y finalización operativa
- filtros y estados adicionales.

### Evolución — baseline URPE-16

#### `clinical_session_logs`
- `id`
- `appointment_id` FK unique + restrict
- `patient_id` FK restrict
- `therapy_id` FK restrict
- `authored_by_user_id` nullable FK hacia `users`
- `status` (`draft` / `completed`)
- `treatment_activities`
- `patient_response` nullable
- `observations_incidents` nullable
- `home_recommendations` nullable
- `next_session_objectives` nullable
- `completed_at` nullable
- timestamps
- índice por paciente + fecha

Cada cita puede tener como máximo una bitácora clínica canónica. El paciente y la terapia se persisten como contexto explícito de la sesión para preservar trazabilidad aun cuando el catálogo o la agenda cambien posteriormente.

#### `clinical_session_log_therapist`
- `clinical_session_log_id` FK cascade hacia la bitácora
- `therapist_id` FK restrict
- timestamps
- combinación única `clinical_session_log_id + therapist_id`

Esta relación registra a los profesionales que efectivamente participaron en la sesión. Se mantiene separada de la asignación actual de agenda para que una modificación posterior no reescriba la autoría/participación clínica histórica.

Pendiente de fases posteriores:
- adjuntos específicos de sesión, si se aprueban;
- mecanismo versionado de corrección o enmienda posterior al cierre.

## Reglas de persistencia

- IDs no deben exponerse como mecanismo de autorización.
- Fechas clínicas y de auditoría conservan precisión suficiente para trazabilidad.
- Relaciones sensibles usan integridad referencial.
- La eliminación física de información clínica no será comportamiento por defecto.
- Las citas se cancelan por estado; no se eliminan físicamente como flujo normal.
- Una cita con bitácora clínica o historial de sustituciones no debe desaparecer mediante cascada destructiva.
- Las series recurrentes son contenedores operativos; las ocurrencias siguen siendo citas individuales auditables.
- Los participantes clínicos de una sesión permanecen persistidos independientemente de cambios posteriores en la asignación de agenda.
- Los cambios de esquema solo entran mediante migraciones versionadas.
- Índices deben cubrir búsquedas por paciente, terapeuta, intervalos de agenda, series recurrentes, bitácoras y auditoría según uso real.
