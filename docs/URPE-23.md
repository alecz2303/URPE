# URPE-23 — Reportes esenciales

## Objetivo

Agregar reportes V1 para operación clínica y seguimiento sin ampliar el acceso al contenido clínico detallado.

## Implementado

- Ruta autenticada `GET /reportes`.
- Permiso granular `reports.view`.
- Acceso baseline para Administrador, Coordinación Clínica y Consulta/Dirección.
- Backfill aditivo e idempotente para instalaciones existentes.
- Filtros por rango de fechas, estado, terapia, terapeuta y paciente.
- KPIs de citas, completadas, no asistencias, cancelaciones y tasa de asistencia.
- Distribución por estados canónicos de las citas.
- Listado paginado de citas.
- Listado paginado de sesiones completadas mostrando únicamente metadatos operativos.
- Relaciones precargadas para evitar N+1 en los listados principales.

## Privacidad y autorización

La pantalla requiere `reports.view` en servidor. El reporte no muestra notas clínicas, evolución, observaciones, recomendaciones, objetivos, enmiendas ni archivos clínicos. El permiso de reportes no concede permisos adicionales sobre expedientes o bitácoras.

## Tasa de asistencia V1

`Completadas / (Completadas + No asistió) × 100`

Las cancelaciones quedan fuera del denominador.

## Exportaciones

URPE-23 no agrega exportaciones descargables. Una exportación futura deberá definir autorización, minimización de datos, auditoría y manejo seguro del archivo generado.

## Cobertura

`EssentialReportsTest` verifica accesos permitidos/denegados, filtros combinados, aislamiento por periodo y catálogo, ausencia de texto clínico sensible en la respuesta e idempotencia del backfill.
