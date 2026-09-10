# URPE-21 — Estados operativos y filtros de Agenda

## Objetivo

Completar el ciclo operativo de las citas y hacer la Agenda más útil para la operación diaria sin mezclar el estado de una cita con el ciclo clínico independiente de su bitácora.

## Estados operativos

- `scheduled` — Programada.
- `confirmed` — Confirmada.
- `in_progress` — En atención.
- `completed` — Completada.
- `no_show` — No asistió.
- `cancelled` — Cancelada.

## Transiciones permitidas

- Programada → Confirmada, En atención o No asistió.
- Confirmada → Programada, En atención o No asistió.
- En atención → Completada.
- Completada, No asistió y Cancelada son estados terminales en el flujo normal.
- La cancelación lógica sólo aplica mientras la cita permanece Programada o Confirmada.

Las transiciones se validan en backend y requieren `appointments.manage`. Cada cambio genera `appointment.status_changed` con estado anterior/nuevo y sin contenido clínico sensible.

## Relación con sesiones clínicas

Los estados de cita y de `clinical_session_logs` son dominios separados. Una bitácora mantiene `draft/completed` independientemente del estado operativo de la cita.

La captura o autoguardado clínico sólo se permite cuando la cita está Programada, Confirmada o En atención. Las citas Completadas, No asistió o Canceladas no pueden iniciar ni continuar captura. Una bitácora ya existente puede seguir consultándose cuando el usuario conserva autorización de lectura.

## Agenda y filtros

Día, Semana y Mes muestran el estado operativo con tratamiento visual consistente. La Agenda permite filtrar por:

- estado;
- terapia;
- terapeuta asignado;
- paciente.

Los filtros se preservan al navegar Anterior/Hoy/Siguiente, cambiar Día/Semana/Mes o seleccionar una fecha directa.

## Compatibilidad de agenda

- Reprogramación sólo para Programada o Confirmada.
- Sustitución de terapeuta no se permite en estados terminales.
- `no_show` libera el intervalo para nuevas programaciones, igual que una cancelación, porque la atención no ocurrió.
- Las operaciones recurrentes siguen usando los servicios existentes y conservan validación atómica.
- No se agregan permisos nuevos ni autorización por nombre de rol.

## Pruebas

`AppointmentOperationalStatusTest` cubre transiciones válidas e inválidas, auditoría, permisos, filtros combinados, bloqueo de captura clínica en estado terminal y no reapertura de No asistió.
