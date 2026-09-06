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

**BR-027.** El perfil operativo de terapeuta puede vincularse a una cuenta interna. Los usuarios con rol Terapeuta deben contar con su perfil operativo sincronizado, sin convertir el nombre del rol en la regla de autorización de las pantallas administrativas.

**BR-028.** La administración de perfiles, disponibilidad y bloqueos de terapeutas se autoriza mediante permisos granulares; `therapists.manage` es el permiso base de URPE-7.

**BR-029.** Cada terapia del catálogo debe persistir como dato configurable con nombre, duración en minutos, cantidad requerida de terapeutas, color de visualización y estado activo/inactivo.

**BR-030.** La duración de una terapia debe ser un entero positivo expresado en minutos; la agenda futura deberá tomar este valor desde el registro de terapia y no desde condicionales por nombre.

**BR-031.** La cantidad requerida de terapeutas debe ser un entero positivo; la agenda futura deberá exigir disponibilidad simultánea de ese número de recursos humanos durante todo el intervalo.

**BR-032.** Las terapias inactivas conservan su registro para integridad histórica y no deben eliminarse de forma destructiva como mecanismo normal de administración.

**BR-033.** Una terapia inactiva no debe estar disponible para nuevas selecciones de agenda, aunque puede permanecer referenciada por información histórica creada previamente.

**BR-034.** El color de una terapia es configuración de presentación y debe almacenarse como un color hexadecimal válido `#RRGGBB`.

**BR-035.** La administración del catálogo de terapias se autoriza mediante el permiso granular `therapies.manage`; Administrador y Coordinación Clínica reciben este permiso en el baseline, mientras que Terapeuta no lo recibe por defecto.

**BR-036.** Vojta y Pediasuit se crean como datos iniciales configurables, no como reglas especiales de código. Sus valores iniciales son Vojta 40 min / 1 terapeuta y Pediasuit 60 min / 2 terapeutas.

## Decisiones abiertas

Estas reglas deben cerrarse antes de desarrollar el recurso correspondiente:

- **BR-P01:** edición de bitácora tras cierre/firma.
- **BR-P02:** quién puede corregir información clínica y mediante qué mecanismo.
- **BR-P03:** comportamiento de series recurrentes al editar una ocurrencia.
- **BR-P04:** efecto de cambiar duración/configuración de terapia sobre citas ya existentes.
- **BR-P05:** Pediasuit requiere exactamente 2 terapeutas o mínimo 2.
- **BR-P06:** campos clínicos visibles para Recepción.
- **BR-P07:** pacientes visibles para Terapeuta según cita/asignación.
- **BR-P08:** mecanismo y permisos para excepciones de citas fuera del horario operativo, si se aprueban.
- **BR-P09:** asignación de terapeutas manual, automática o mixta.
- **BR-P10:** cualificaciones requeridas por terapia, si aplican.
