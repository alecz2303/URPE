# Roadmap — URPE Gestión Clínica

## Fase 0 — Fundación

- [x] **URPE-1** Documentación canónica, workflow, stack y repositorio.
- [x] **URPE-2** Bootstrap completo Laravel, CI y configuración de ambientes.

## Fase 1 — Plataforma y seguridad

1. [x] **URPE-3** Autenticación.
2. [x] **URPE-4** Usuarios, roles y permisos granulares.
3. [x] **URPE-5** Auditoría base y política de archivos clínicos.
4. [x] **URPE-6** Configuración general del centro y horarios.

## Fase 2 — Estructura clínica

1. [x] **URPE-7** Terapeutas y disponibilidad individual.
2. [x] **URPE-8** Catálogo configurable de terapias.
3. [x] **URPE-9** Pacientes, tutores/responsables y datos administrativos.
4. [x] **URPE-10** Base del expediente clínico: antecedentes, diagnósticos, objetivos terapéuticos y observaciones generales, separados de la evolución por sesión.

## Fase 3 — Agenda / calendario — prioridad crítica

1. [x] Vista día/semana/mes y dashboard. *(URPE-11 base funcional; URPE-12 presentación UX diferenciada)*
2. [x] Crear, editar, reprogramar y cancelar citas. *(URPE-11)*
3. [x] Duración automática según terapia. *(URPE-11)*
4. [x] Asignación de uno o varios terapeutas. *(URPE-11)*
5. [x] Validación de horario operativo del centro durante toda la cita. *(URPE-11)*
6. [x] Validación de disponibilidad de todos los terapeutas requeridos durante toda la cita. *(URPE-11)*
7. [x] Prevención de traslapes. *(URPE-11)*
8. [ ] Citas recurrentes.
9. [x] Ausencias, bloqueos y descansos como restricciones de agenda. *(URPE-11 reutiliza baseline URPE-7)*
10. [ ] Estados y filtros operativos ampliados.
11. [x] Historial básico auditable de creación, reprogramación y cancelación. *(URPE-11)*
12. [x] Presentación diferenciada: día cronológico, semana por columnas y mes en calendario. *(URPE-12)*

## Fase 4 — Expediente y evolución

1. [x] Historia clínica base. *(URPE-10)*
2. [x] Diagnósticos, objetivos y observaciones basales. *(URPE-10)*
3. [ ] Archivos clínicos protegidos vinculados al expediente.
4. [ ] Bitácora por sesión.
5. [ ] Línea de tiempo clínica.
6. [ ] Reglas de cierre/corrección y trazabilidad.

## Fase 5 — Operación y cierre V1

1. [x] Dashboard operativo y navegación clínica base. *(URPE-12)*
2. [x] Shell autenticado con sidebar persistente y menú móvil. *(URPE-12)*
3. [x] Separación UX de listado, consulta y edición en recursos clínicos/administrativos principales. *(URPE-12)*
4. [ ] Reportes esenciales.
5. [ ] Cobertura final de permisos y auditoría.
6. [ ] Regresión, seguridad, rendimiento y respaldo.
7. [ ] UAT y despliegue.

## V1.x / V2

- WhatsApp y automatizaciones de comunicación.
- Integraciones adicionales.
- Funciones administrativas/financieras no aprobadas para V1.
