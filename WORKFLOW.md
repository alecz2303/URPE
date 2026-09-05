# Workflow canónico — URPE Gestión Clínica

1. Documentación canónica primero.
2. Jira gobierna el trabajo: Roadmap → Jira → desarrollo → tests → documentación → cierre.
3. Una tarea activa a la vez salvo acuerdo explícito.
4. Trabajamos por bloques completos y, cuando corresponda, se entrega `URPE-XX-bloque-N.zip`.
5. No reconstruir el proyecto una vez establecida una base.
6. No romper funcionalidad existente.
7. Tests forman parte de la funcionalidad.
8. No cerrar tareas con tests rojos.
9. Git debe contar la misma historia que Jira.
10. No PR/merge antes de completar, probar y documentar el bloque.
11. `master` es siempre la última versión estable.
12. No inventar estado de proyecto, Git, Jira o tests: se verifica.
13. Los ZIP/proyecto real son fuente técnica de verdad cuando se suministran.
14. Preferir archivos completos dentro de bloques sobre parches manuales importantes.
15. Cambios estructurales de BD se hacen con migraciones.
16. Nada importante debe hardcodearse si es configuración/dato de negocio.
17. Seguridad desde el inicio.
18. Auditar acciones sensibles.
19. Eliminación clínica extremadamente controlada.
20. Alcance V1 se congela al aprobar `REQUIREMENTS.md`; nuevas ideas van a backlog/V1.x/V2.
21. No sobrearquitecturar.
22. Cada tarea tiene Definition of Done.
23. Cada bug corregido debe generar regresión cuando sea aplicable.
24. Al cambiar de chat se prepara handoff canónico con rama, Jira, commit estable, bloque, tests, pendientes y decisiones.
25. No saltar a la siguiente tarea/bloque antes de cerrar correctamente el actual.

## Protección de `master`

- `master` es la rama estable y predeterminada.
- No se permiten pushes directos a `master`.
- Todo cambio entra mediante Pull Request.
- Cada PR hacia `master` requiere mínimo 1 aprobación.
- Force push y eliminación de `master` están bloqueados.
- No existe bypass operativo para saltarse la regla.
- El único cambio directo excepcional fue el bootstrap inicial necesario para crear una rama en un repositorio vacío.

## Convención Git/Jira

- Rama: `URPE-<número>`.
- Commit: `URPE-<número> <tipo>: <descripción>`.
- Tipos sugeridos: `feat`, `fix`, `test`, `docs`, `refactor`, `chore`.
- PR: referencia Jira y resume alcance, tests y riesgos.

## Definition of Done general

Según aplique:
- código completo;
- reglas de negocio implementadas;
- permisos;
- auditoría;
- tests;
- regresión verde;
- documentación actualizada;
- revisión funcional;
- PR aprobado antes de merge.
