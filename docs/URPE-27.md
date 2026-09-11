# URPE-27 — Cobertura final de permisos y auditoría

## Objetivo

Cerrar la revisión transversal de autorización y auditoría de V1 sin ampliar el alcance funcional ni crear permisos redundantes.

## Revisión de autorización

La matriz baseline conserva mínimo privilegio:

- **Administrador:** todos los permisos registrados.
- **Coordinación Clínica:** terapias, pacientes, expediente, agenda, bitácoras globales y reportes; no recibe gestión de usuarios/roles/centro salvo grant explícito.
- **Terapeuta:** `session_logs.view` y `session_logs.manage`; el alcance clínico continúa condicionado por asignación actual o participación histórica según cada acción.
- **Recepción:** pacientes y agenda en modalidad operativa; sin expediente, bitácoras ni reportes por defecto.
- **Consulta/Dirección:** lectura de pacientes, agenda y reportes; sin modificación clínica ni administrativa por defecto.

Se agregan regresiones que fijan esta matriz y verifican denegación por URL directa en superficies representativas de seguridad y reportes. La autorización server-side sigue siendo la fuente de verdad; ocultar enlaces o botones no concede ni revoca acceso.

## Hardening de auditoría

`AuditTrail` ya eliminaba secretos y credenciales por clave. URPE-27 agrega una segunda barrera defensiva para nombres de campos de narrativa clínica conocidos: antecedentes, diagnósticos, objetivos, observaciones, actividades, evolución, recomendaciones, objetivos de siguiente sesión y contenido de enmiendas.

Esto no sustituye la minimización en cada llamada a `AuditTrail::record()`: los eventos deben seguir enviando identificadores, estados, nombres de secciones y contexto operativo mínimo. El sanitizador evita que una futura llamada accidental replique texto clínico bajo esas claves conocidas.

## Pruebas

- matriz exacta de permisos baseline para Coordinación Clínica, Terapeuta, Recepción y Consulta/Dirección;
- denegación por URL directa a reportes/usuarios/roles/centro para roles sin permisos;
- confirmación de que Terapeuta no obtiene permisos operativos o clínicos globales;
- sanitización recursiva de narrativa clínica en metadata de auditoría;
- regresión completa mediante la suite existente de agenda, pacientes, expediente, sesiones, archivos, reportes, roles y permisos.

## Decisiones

- No se crean permisos nuevos: los 20 permisos existentes cubren las superficies V1 actuales.
- No se cambian grants baseline ni se agregan migraciones de permisos en este ticket.
- No se registra contenido clínico completo en auditoría.
- La siguiente etapa del roadmap es regresión, seguridad, rendimiento y respaldo.
