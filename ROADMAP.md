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

1. [x] Vista día/semana/mes y dashboard. *(URPE-11 base funcional; URPE-12 presentación UX diferenciada; URPE-13 navegación temporal y refinamiento visual)*
2. [x] Crear, editar, reprogramar y cancelar citas. *(URPE-11; URPE-14 agrega alcance por serie recurrente)*
3. [x] Duración automática según terapia. *(URPE-11)*
4. [x] Asignación de uno o varios terapeutas. *(URPE-11; URPE-16 agrega sustitución operativa auditable de último momento)*
5. [x] Validación de horario operativo del centro durante toda la cita. *(URPE-11)*
6. [x] Validación de disponibilidad de todos los terapeutas requeridos durante toda la cita. *(URPE-11; URPE-14 agrega consulta anticipada de horarios y terapeutas disponibles; URPE-16 reutiliza las mismas reglas al sustituir terapeuta)*
7. [x] Prevención de traslapes. *(URPE-11; URPE-16 la conserva en sustituciones)*
8. [x] Citas recurrentes. *(URPE-14: series semanales acotadas, ocurrencias individuales, validación atómica y edición/cancelación por alcance)*
9. [x] Ausencias, bloqueos y descansos como restricciones de agenda. *(URPE-11 reutiliza baseline URPE-7; URPE-13 agrega acceso dedicado fuera de la edición del terapeuta)*
10. [x] Estados y filtros operativos ampliados. *(URPE-21: estados Programada/Confirmada/En atención/Completada/No asistió/Cancelada; filtros por estado, terapia, terapeuta y paciente preservados entre Día/Semana/Mes.)*
11. [x] Historial básico auditable de creación, reprogramación y cancelación. *(URPE-11; URPE-14 agrega auditoría de operaciones sobre series; URPE-16 agrega historial de sustituciones de terapeuta; URPE-21 audita cambios de estado.)*
12. [x] Presentación diferenciada: día cronológico, semana por columnas y mes en calendario. *(URPE-12)*
13. [x] Navegación anterior/hoy/siguiente, selector de fecha y acceso de mes a día. *(URPE-13)*
14. [x] Selector asistido de disponibilidad para nuevas citas. *(URPE-14: fecha → horarios válidos → terapeutas disponibles)*

## Fase 4 — Expediente y evolución

1. [x] Historia clínica base. *(URPE-10; presentación y edición refinadas en URPE-12/URPE-13)*
2. [x] Diagnósticos, objetivos y observaciones basales. *(URPE-10)*
3. [ ] Archivos clínicos protegidos vinculados al expediente.
4. [x] Bitácora por sesión. *(URPE-16: una bitácora por cita, borrador/cierre, contenido clínico estructurado y participantes reales; URPE-20 la convierte en un workspace de sesión con autoguardado, guardar y salir y acceso global.)*
5. [x] Línea de tiempo clínica. *(URPE-16 aporta historial cronológico básico; URPE-17 lo convierte en una línea longitudinal de evolución con estado, terapia, participantes, resumen clínico e indicador de enmiendas; URPE-20 muestra contexto reciente durante la captura.)*
6. [x] Reglas de corrección posterior al cierre. *(URPE-17: la nota completada permanece inmutable y cualquier corrección posterior se agrega como enmienda clínica trazable.)*
7. [x] Localizador global de sesiones clínicas. *(URPE-20: búsqueda por paciente, filtros de fecha/terapia/terapeuta/estado y acciones contextuales según autorización.)*

## Fase 5 — Operación y cierre V1

1. [x] Dashboard operativo y navegación clínica base. *(URPE-12; sistema visual refinado en URPE-13; URPE-16 agrega acceso del terapeuta a sus sesiones asignadas; URPE-20 agrega navegación global de Sesiones clínicas.)*
2. [x] Shell autenticado con sidebar persistente y menú móvil. *(URPE-12; refinamiento visual URPE-13)*
3. [x] Separación UX de listado, consulta y edición en recursos clínicos/administrativos principales. *(URPE-12; URPE-20 extiende el patrón a Sesiones clínicas.)*
4. [x] Sistema visual clínico consistente y más legible en agenda, pacientes, expediente, terapeutas, terapias, usuarios, roles y centro. *(URPE-13)*
5. [ ] Reportes esenciales.
6. [ ] Cobertura final de permisos y auditoría.
7. [ ] Regresión, seguridad, rendimiento y respaldo.
8. [ ] UAT y despliegue.

## V1.x / V2

- WhatsApp y automatizaciones de comunicación.
- Integraciones adicionales.
- Funciones administrativas/financieras no aprobadas para V1.
