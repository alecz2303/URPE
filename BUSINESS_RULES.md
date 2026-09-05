# Business Rules — URPE Gestión Clínica

## Reglas aprobadas

**BR-001.** La agenda/calendario es núcleo de V1.

**BR-002.** Horario inicial del centro: 09:00–18:00, almacenado como configuración editable.

**BR-003.** Vojta inicia con duración 40 minutos y requiere 1 terapeuta.

**BR-004.** Pediasuit inicia con duración 60 minutos y requiere 2 terapeutas.

**BR-005.** Terapias, duración, cantidad requerida de terapeutas, color, estado y atributos configurables no se hardcodean por nombre.

**BR-006.** Agregar terapeutas o tipos de terapia no debe requerir modificar código.

**BR-007.** Una cita solo es válida si todos los recursos humanos requeridos están disponibles durante la totalidad del intervalo.

**BR-008.** Un terapeuta no puede participar en dos citas cuyos intervalos se traslapen.

**BR-009.** Deben existir reprogramación, cancelación, no-show y finalización de citas.

**BR-010.** Se permiten citas recurrentes.

**BR-011.** La disponibilidad individual contempla horario, ausencias, descansos y bloqueos.

**BR-012.** La agenda debe ser visible desde el dashboard.

**BR-013.** El sistema es multiusuario y la autorización se basa en permisos granulares.

**BR-014.** Un terapeuta no obtiene automáticamente acceso clínico irrestricto a todos los pacientes.

**BR-015.** El expediente admite historia, imágenes, radiografías, estudios y documentos.

**BR-016.** Una sesión terapéutica puede generar una bitácora/evolución vinculada a paciente, cita y terapeuta(s).

**BR-017.** Acciones sensibles deben dejar trazabilidad de actor, acción y momento.

**BR-018.** Información clínica importante no se elimina silenciosamente; cada recurso definirá archivo, anulación, soft delete o versionado según corresponda.

**BR-019.** WhatsApp y comunicación automatizada están fuera de V1.

## Decisiones abiertas

Estas reglas deben cerrarse antes de desarrollar el recurso correspondiente:

- **BR-P01:** edición de bitácora tras cierre/firma.
- **BR-P02:** quién puede corregir información clínica y mediante qué mecanismo.
- **BR-P03:** comportamiento de series recurrentes al editar una ocurrencia.
- **BR-P04:** efecto de cambiar duración/configuración de terapia sobre citas ya existentes.
- **BR-P05:** Pediasuit requiere exactamente 2 terapeutas o mínimo 2.
- **BR-P06:** campos clínicos visibles para Recepción.
- **BR-P07:** pacientes visibles para Terapeuta según cita/asignación.
- **BR-P08:** excepciones para citas fuera del horario operativo.
- **BR-P09:** asignación de terapeutas manual, automática o mixta.
- **BR-P10:** cualificaciones requeridas por terapia, si aplican.
