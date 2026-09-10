# URPE-22 — Archivos clínicos protegidos vinculados al expediente

## Objetivo

Integrar documentos, imágenes, radiografías y estudios al expediente clínico del paciente reutilizando la infraestructura privada de `ClinicalFileStorage` implementada en URPE-5, sin crear un segundo mecanismo de almacenamiento ni ampliar permisos clínicos por nombre de rol.

## Alcance implementado

- `ClinicalRecord` expone una relación polimórfica `files()` hacia `ClinicalFile`.
- `ClinicalFile` resuelve su `subject()` para autorización contextual.
- La carga de archivos se realiza desde el expediente clínico y requiere `clinical_records.manage`.
- La descarga de archivos vinculados a un expediente requiere `clinical_records.view` en el momento del acceso.
- Los archivos clínicos legacy sin `ClinicalRecord` como subject conservan el permiso `clinical_files.download` para compatibilidad.
- El expediente lista únicamente archivos activos vinculados a ese mismo registro.
- El retiro exige `clinical_records.manage`, valida que archivo y paciente pertenezcan al mismo expediente y usa soft delete.
- El retiro lógico conserva los bytes físicos y la auditoría existente.
- La interfaz permite clasificar archivos como documento, imagen clínica, radiografía, estudio u otro y registrar una descripción breve opcional.
- Formatos V1 admitidos: PDF, JPG, JPEG, PNG y WEBP, máximo 20 MB por archivo.

## Persistencia

No se agrega una tabla nueva. Se reutiliza `clinical_files`:

- `subject_type` + `subject_id` apuntan al `ClinicalRecord`.
- `metadata.category` almacena `document`, `image`, `radiograph`, `study` u `other`.
- `metadata.description` conserva una descripción opcional de hasta 500 caracteres.
- `sha256`, MIME, extensión, tamaño y nombre original mantienen trazabilidad técnica.
- El binario permanece en el disco privado `clinical`, fuera de `public/`.

## Reglas de negocio cerradas

**BR-090.** Los archivos longitudinales del expediente se vinculan al `ClinicalRecord` mediante `clinical_files.subject_type/subject_id`; no se duplica el archivo físico ni se crea un almacén alterno.

**BR-091.** Cargar o retirar archivos del expediente requiere `clinical_records.manage`; consultar o descargar un archivo vinculado requiere `clinical_records.view`. Conocer el UUID no concede autorización.

**BR-092.** Los archivos retirados dejan de aparecer en el expediente normal mediante soft delete, pero conservan bytes físicos y trazabilidad; la eliminación destructiva no forma parte del flujo V1.

**BR-093.** Un archivo sólo puede retirarse desde el mismo expediente/paciente al que pertenece; el backend valida la relación y rechaza operaciones cruzadas entre pacientes.

**BR-094.** La clasificación y descripción del adjunto son metadata operativa mínima y no sustituyen el expediente ni la bitácora clínica; la auditoría no copia el contenido binario ni textos clínicos extensos.

## Seguridad

La autorización de descarga es contextual. Para archivos cuyo `subject` es un `ClinicalRecord`, el controlador verifica `clinical_records.view`. Así, un UUID válido por sí solo no abre una vía de acceso. La UI no constituye la barrera de seguridad: carga, descarga y retiro vuelven a validar permisos en servidor.

La política legacy de `clinical_files.download` se conserva únicamente para archivos que no estén vinculados a un `ClinicalRecord`, evitando una ruptura retroactiva de la infraestructura de URPE-5.

## Auditoría

Se reutilizan los eventos existentes:

- `clinical_file.stored`
- `clinical_file.downloaded`
- `clinical_file.retired`

La auditoría registra identificadores, hash y contexto técnico mínimo. No almacena el contenido del archivo.

## Pruebas

`ClinicalRecordFileTest` cubre:

- carga autorizada y vínculo al expediente;
- validación de tipo y requisito de expediente existente;
- listado aislado por paciente y exclusión de archivos retirados;
- descarga contextual con `clinical_records.view` sin exigir el permiso legacy global;
- denegación a usuarios sin acceso clínico aunque conozcan el UUID;
- retiro lógico con conservación del archivo físico y auditoría;
- rechazo de retiro mediante el expediente de otro paciente.

La suite existente `ClinicalFileSecurityTest` continúa validando almacenamiento privado, nombre generado, hash, auditoría y descarga legacy protegida.

## Impacto de esquema

No requiere migración. El esquema de `clinical_files` ya contenía la relación polimórfica necesaria desde URPE-5.

## Resultado de V1

Con URPE-22 queda cubierto el pendiente de Fase 4 y del backlog V1 correspondiente a documentos, imágenes, radiografías y estudios vinculados al expediente clínico.
