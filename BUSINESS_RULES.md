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

**BR-020.** Una cita ordinaria debe quedar completamente contenida dentro de una ventana operativa habilitada del centro; no puede atravesar un periodo cerrado entre dos ventanas ni extenderse fuera del horario operativo. Las excepciones, si se autorizan posteriormente, deberán definirse de forma explícita y auditable.

**BR-021.** La disponibilidad semanal de un terapeuta está subordinada al horario operativo del centro; ninguna ventana individual puede extenderse fuera de una ventana operativa habilitada del mismo día.

**BR-022.** Un terapeuta puede tener múltiples ventanas de disponibilidad en un mismo día para representar descansos u horarios divididos, pero dichas ventanas no pueden traslaparse.

**BR-023.** Una cita que requiera a un terapeuta debe quedar completamente contenida dentro de una única ventana de disponibilidad habilitada de ese terapeuta y no puede atravesar descansos entre ventanas.

**BR-024.** Un terapeuta inactivo se considera no disponible para nuevas asignaciones independientemente de su horario semanal configurado.

**BR-025.** Las ausencias y bloqueos del terapeuta son restricciones explícitas de agenda; cualquier intervalo de cita que se traslape con uno de ellos se considera no disponible.

**BR-026.** Un bloqueo que termina exactamente cuando inicia una cita, o inicia exactamente cuando termina, no se considera traslape; los intervalos se evalúan con límites adyacentes permitidos.

**BR-027.** El perfil operativo de terapeuta conserva un vínculo técnico obligatorio con una cuenta interna mediante `therapists.user_id`. En el flujo normal de URPE, crear un terapeuta aprovisiona automáticamente su usuario y rol Terapeuta; la selección manual de "Usuario vinculado" deja de formar parte de la experiencia administrativa ordinaria.

**BR-028.** La administración de perfiles, disponibilidad y bloqueos de terapeutas se autoriza mediante permisos granulares; `therapists.manage` es el permiso base de URPE-7.

**BR-029.** Cada terapia del catálogo debe persistir como dato configurable con nombre, duración en minutos, cantidad requerida de terapeutas, color de visualización y estado activo/inactivo.

**BR-030.** La duración de una terapia debe ser un entero positivo expresado en minutos; la agenda futura deberá tomar este valor desde el registro de terapia y no desde condicionales por nombre.

**BR-031.** La cantidad requerida de terapeutas debe ser un entero positivo; la agenda futura deberá exigir disponibilidad simultánea de ese número de recursos humanos durante todo el intervalo.

**BR-032.** Las terapias inactivas conservan su registro para integridad histórica y no deben eliminarse de forma destructiva como mecanismo normal de administración.

**BR-033.** Una terapia inactiva no debe estar disponible para nuevas selecciones de agenda, aunque puede permanecer referenciada por información histórica creada previamente.

**BR-034.** El color de una terapia es configuración de presentación y debe almacenarse como un color hexadecimal válido `#RRGGBB`.

**BR-035.** La administración del catálogo de terapias se autoriza mediante el permiso granular `therapies.manage`; Administrador y Coordinación Clínica reciben este permiso en el baseline, mientras que Terapeuta no lo recibe por defecto.

**BR-036.** Vojta y Pediasuit se crean como datos iniciales configurables, no como reglas especiales de código. Sus valores iniciales son Vojta 40 min / 1 terapeuta y Pediasuit 60 min / 2 terapeutas.

**BR-037.** Cada paciente debe conservar un folio interno estable con formato `URPE-AAAA-NNNNNN`; la numeración es consecutiva e independiente por año.

**BR-038.** Un paciente puede tener uno o varios tutores/responsables administrativos vinculados.

**BR-039.** La relación paciente-responsable puede registrar parentesco o relación y únicamente un responsable puede quedar marcado como principal para un mismo paciente.

**BR-040.** Los responsables administrativos no se convierten en usuarios autenticados del sistema por el hecho de estar vinculados a un paciente.

**BR-041.** Los pacientes inactivos permanecen persistidos para integridad histórica; desactivar no equivale a eliminar.

**BR-042.** La administración de pacientes se protege mediante permisos granulares `patients.view` y `patients.manage`; no se autoriza por nombre de rol dentro de controladores o vistas.

**BR-043.** El rol Terapeuta no recibe acceso global a pacientes por defecto. En URPE-16 el acceso clínico operativo a bitácoras se limita a citas donde el usuario autenticado se resuelve al terapeuta asignado o a un participante histórico válido de esa sesión.

**BR-044.** Los cambios administrativos relevantes de pacientes y responsables deben ser auditables sin almacenar innecesariamente payloads sensibles completos.

**BR-045.** Cada paciente puede tener como máximo un expediente clínico base activo dentro del baseline de URPE-10.

**BR-046.** El expediente clínico base almacena antecedentes médicos, antecedentes prenatales/perinatales, antecedentes del desarrollo, antecedentes familiares, diagnósticos, objetivos terapéuticos y observaciones clínicas generales.

**BR-047.** El expediente clínico base se mantiene separado de los datos administrativos del paciente y de sus responsables; las notas administrativas no sustituyen ni duplican información clínica.

**BR-048.** La evolución por sesión, actividades realizadas, respuesta del paciente, progreso, incidencias, recomendaciones domiciliarias y objetivos de próxima sesión pertenecen a la bitácora clínica por cita y no al expediente clínico base.

**BR-049.** El acceso al expediente clínico se autoriza mediante permisos granulares `clinical_records.view` y `clinical_records.manage`; no se autoriza por nombre de rol dentro de controladores o vistas.

**BR-050.** Administrador y Coordinación Clínica conservan acceso clínico amplio conforme a permisos granulares. El rol Terapeuta no recibe acceso clínico global; URPE-16 autoriza su bitácora por asignación/participación válida en la cita.

**BR-051.** La creación y actualización del expediente clínico deben auditar actor, paciente y secciones afectadas sin duplicar en metadata de auditoría los textos clínicos sensibles completos.

**BR-052.** La existencia de un expediente clínico base impide la eliminación destructiva del paciente; la integridad clínica prevalece sobre el borrado físico.

**BR-053.** La ficha administrativa del paciente puede exponer un acceso al expediente clínico únicamente a usuarios autorizados, sin mostrar contenido clínico dentro de la ficha administrativa.

**BR-054.** El modelo del expediente clínico base debe quedar preparado para futuras relaciones con archivos clínicos protegidos, pero URPE-10 no implementa la administración completa de adjuntos.

**BR-055.** Cada cita clínica vincula exactamente un paciente y una terapia; no duplica contenido del expediente clínico ni evolución de sesión.

**BR-056.** La duración y hora de término de una cita se derivan de `duration_minutes` de la terapia vigente al crear o reprogramar la cita; no se hardcodean por nombre de terapia.

**BR-057.** La cantidad de terapeutas asignados a una cita debe coincidir exactamente con `required_therapists` de la terapia en el baseline de URPE-11.

**BR-058.** Para crear o reprogramar una cita, paciente, terapia y todos los terapeutas asignados deben estar activos.

**BR-059.** El intervalo completo de la cita debe quedar dentro del horario operativo habilitado del centro y dentro de la disponibilidad de cada terapeuta asignado, sin intersección con ausencias o bloqueos.

**BR-060.** Un terapeuta no puede estar asignado a dos citas activas cuyos intervalos se traslapen; las citas canceladas liberan el intervalo para nuevas programaciones.

**BR-061.** La reprogramación mantiene la identidad de la cita y puede modificar terapia, terapeutas y horario, recalculando duración y fin conforme a la terapia seleccionada.

**BR-062.** La cancelación es un cambio de estado y no una eliminación física. Debe conservar la cita, marcar fecha de cancelación y permitir motivo opcional sin duplicarlo en metadata de auditoría.

**BR-063.** La agenda base se autoriza mediante `appointments.view` y su administración mediante `appointments.manage`; controladores y vistas no dependen de nombres de rol.

**BR-064.** Crear, reprogramar y cancelar citas deben generar eventos de auditoría con identificadores y tiempos necesarios para trazabilidad, evitando payloads clínicos o motivos sensibles completos.

**BR-065.** Una serie recurrente semanal debe tener fecha inicial, fecha final y al menos un día de la semana; no se permiten series abiertas o infinitas en V1.

**BR-066.** Cada ocurrencia de una serie recurrente se persiste como una cita individual y conserva las mismas validaciones de horario, disponibilidad, bloqueos, cantidad de terapeutas y traslapes que una cita ordinaria.

**BR-067.** La creación de una serie recurrente es atómica: si alguna ocurrencia no es válida, no debe persistirse ninguna cita de esa serie.

**BR-068.** Las citas de una serie pueden editarse o cancelarse con alcance `solo esta cita`, `esta y las siguientes` o `toda la serie`; las operaciones sobre múltiples ocurrencias deben validarse completas antes de persistir cambios.

**BR-069.** El flujo de creación de citas debe poder consultar horarios candidatos y terapeutas disponibles antes de guardar, usando las mismas restricciones de agenda como ayuda operativa; la validación final del backend sigue siendo obligatoria al persistir.

**BR-070.** Un horario solo se presenta como disponible para una terapia cuando existe capacidad simultánea de al menos `required_therapists` terapeutas activos durante todo el intervalo calculado.

**BR-071.** Crear un terapeuta es una operación atómica de negocio: se crean el perfil profesional y la cuenta interna, se asigna el rol Terapeuta y se establece `therapists.user_id`. Si falla una parte, no debe persistir un perfil o usuario huérfano.

**BR-072.** El correo del terapeuta funciona como identificador de acceso y debe ser único respecto de cuentas y perfiles existentes. Editar nombre, correo o estado del terapeuta mantiene sincronizada su cuenta interna.

**BR-073.** La desactivación de un terapeuta desactiva también su acceso autenticado, pero no elimina citas, bitácoras, auditoría ni relaciones históricas.

**BR-074.** Cada ocurrencia de cita puede tener como máximo una bitácora clínica canónica, separada del expediente clínico base y de las notas administrativas.

**BR-075.** La bitácora clínica admite estado `draft` y `completed`. Una bitácora completada queda cerrada para edición normal en el baseline de URPE-16; cualquier mecanismo de corrección posterior deberá preservar el registro original y su trazabilidad.

**BR-076.** La bitácora registra tratamiento/actividades realizadas, respuesta/evolución del paciente, observaciones/incidencias, recomendaciones para casa y objetivos de siguiente sesión, además de los terapeutas participantes.

**BR-077.** Los terapeutas participantes de la bitácora deben ser un subconjunto de los terapeutas efectivamente asignados a la cita al momento de la captura. Un terapeuta no puede autoasignarse ni agregarse a una sesión desde la bitácora para obtener acceso.

**BR-078.** El acceso de un terapeuta a una bitácora requiere simultáneamente permiso granular y una relación válida con la cita: asignación vigente o participación clínica histórica ya persistida. El rol Terapeuta por sí solo no basta.

**BR-079.** Coordinación Clínica y Administrador pueden administrar bitácoras sin restricción por asignación mediante `session_logs.manage_all`; esta excepción sigue siendo permission-based y no depende de nombres de rol dentro de controladores.

**BR-080.** Una sustitución de terapeuta de último momento solo puede ejecutarla un usuario con permiso para administrar agenda. Debe validar terapeuta activo, disponibilidad, bloqueos, traslapes y mantener la cantidad exacta de terapeutas requerida por la terapia.

**BR-081.** Toda sustitución conserva historial de terapeuta removido, terapeuta agregado, actor, momento y motivo operativo opcional. La asignación efectiva cambia de inmediato, pero el historial anterior no se borra.

**BR-082.** Después de una sustitución válida, el terapeuta sustituto obtiene acceso a la sesión por la nueva asignación y el terapeuta removido deja de tener acceso de edición por asignación, salvo que ya permanezca registrado como participante histórico de una bitácora existente y el acceso solicitado sea de consulta.

**BR-083.** El contenido clínico completo de una bitácora no debe duplicarse en metadata de auditoría. Los eventos de auditoría registran identificadores, estado, participantes y timestamps suficientes para trazabilidad.

**BR-084.** La ficha del paciente puede mostrar un historial cronológico de sesiones únicamente a usuarios con autorización clínica correspondiente; este historial no convierte el permiso de pacientes administrativos en permiso clínico.

## Decisiones abiertas

Estas reglas deben cerrarse antes de desarrollar el recurso correspondiente:

- **BR-P01:** mecanismo de corrección/enmienda de bitácora después del cierre, preservando el registro original.
- **BR-P02:** quién puede autorizar y firmar una corrección clínica posterior al cierre.
- **BR-P04:** efecto de cambiar duración/configuración de terapia sobre citas ya existentes.
- **BR-P05:** Pediasuit requiere exactamente 2 terapeutas o mínimo 2. *(URPE-11 adopta exactamente `required_therapists` como baseline; revisar solo si negocio cambia la semántica futura.)*
- **BR-P06:** campos clínicos visibles para Recepción.
- **BR-P07:** alcance de datos administrativos del paciente visibles para Terapeuta fuera del contexto puntual de una cita/bitácora.
- **BR-P08:** mecanismo y permisos para excepciones de citas fuera del horario operativo, si se aprueban.
- **BR-P09:** asignación de terapeutas manual, automática o mixta. *(URPE-14 mantiene selección manual asistida por disponibilidad; URPE-16 agrega sustitución autorizada de último momento.)*
- **BR-P10:** cualificaciones requeridas por terapia, si aplican.
