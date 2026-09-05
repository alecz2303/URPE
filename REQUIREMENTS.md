# Requirements — URPE Gestión Clínica V1

## Alcance funcional

### Acceso y autenticación

- Inicio de sesión mediante credenciales locales y sesión web.
- Cierre de sesión seguro.
- Regeneración de sesión al autenticar e invalidación al cerrar sesión.
- Mensajes de credenciales inválidas que no revelen si una cuenta existe.
- Rutas exclusivas para invitados y usuarios autenticados.
- Autenticación separada de la autorización: roles y permisos granulares se implementan como capacidad posterior.

### Agenda y calendario

La agenda es una capacidad crítica de V1 y debe ser visible desde el dashboard.

Debe permitir:
- vistas diaria, semanal y mensual;
- alta, edición, reprogramación y cancelación;
- citas recurrentes;
- búsqueda/selección de paciente;
- selección de terapia;
- asignación de terapeutas;
- duración calculada desde la configuración de terapia;
- validación de disponibilidad;
- prevención de traslapes;
- horarios del centro configurables;
- horarios, ausencias, bloqueos y descansos por terapeuta;
- colores por terapia;
- estados operativos;
- filtros por terapeuta, terapia, paciente y estado;
- acceso desde cita a expediente y bitácora relacionada;
- historial básico de reprogramaciones/cambios.

### Terapias

Datos iniciales configurables:
- Vojta: 40 minutos, 1 terapeuta.
- Pediasuit: 60 minutos, 2 terapeutas.

Ninguna de estas reglas se implementará mediante condiciones hardcodeadas por nombre.

### Pacientes y expediente

- Datos identificativos y administrativos.
- Tutor(es)/responsable(s).
- Historia clínica.
- Diagnósticos.
- Objetivos terapéuticos.
- Observaciones clínicas.
- Fotos, radiografías, estudios, PDFs y otros documentos.
- Línea de tiempo clínica.

### Bitácora/evolución

Por sesión:
- fecha/hora;
- terapia;
- terapeuta(s);
- objetivos trabajados;
- actividades;
- observaciones;
- respuesta del paciente;
- avances;
- dificultades/incidentes;
- recomendaciones;
- adjuntos;
- próximos objetivos.

### Multiusuario

Roles iniciales de referencia:
- Administrador;
- Coordinación clínica;
- Terapeuta;
- Recepción;
- Consulta/Dirección.

La autorización debe basarse en roles y permisos granulares, no en lógica rígida por nombre de rol.

### Dashboard

Debe priorizar operación:
- agenda visible;
- citas del día y próximas;
- disponibilidad relevante;
- alertas;
- accesos rápidos.

## No funcionales

- Laravel 13 / PHP 8.4.
- Aplicación web responsive.
- Autorización backend obligatoria.
- Auditoría de acciones sensibles.
- Archivos clínicos privados.
- Migraciones para cambios de esquema.
- Pruebas automatizadas como parte de Definition of Done.
- `master` estable y protegida.

## Exclusiones V1

- WhatsApp.
- Recordatorios/confirmaciones automáticas por WhatsApp.
- Reprogramación/cancelación vía WhatsApp.
- Integraciones externas no aprobadas.

## Pendientes de decisión antes de implementar el recurso afectado

- Política exacta de cierre/firma y corrección de bitácoras.
- Visibilidad clínica exacta de Recepción.
- Alcance de pacientes visibles para cada terapeuta.
- Política de excepción para citas fuera del horario general.
- Asignación manual, automática o mixta de terapeutas.
- Requisitos de especialidad/cualificación por terapia.
