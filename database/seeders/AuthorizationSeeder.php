<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = collect([
            ['name' => 'Ver usuarios', 'slug' => 'users.view', 'description' => 'Consultar cuentas internas de URPE.'],
            ['name' => 'Crear usuarios', 'slug' => 'users.create', 'description' => 'Crear cuentas internas de URPE.'],
            ['name' => 'Actualizar usuarios', 'slug' => 'users.update', 'description' => 'Modificar cuentas internas de URPE.'],
            ['name' => 'Desactivar usuarios', 'slug' => 'users.deactivate', 'description' => 'Desactivar cuentas internas de URPE.'],
            ['name' => 'Ver roles y permisos', 'slug' => 'roles.view', 'description' => 'Consultar la configuración de roles y permisos.'],
            ['name' => 'Administrar roles y permisos', 'slug' => 'roles.manage', 'description' => 'Modificar asignaciones de roles y permisos.'],
            ['name' => 'Descargar archivos clínicos', 'slug' => 'clinical_files.download', 'description' => 'Descargar adjuntos clínicos protegidos tras autorización.'],
            ['name' => 'Administrar configuración del centro', 'slug' => 'center.manage', 'description' => 'Modificar configuración general y horarios operativos del centro.'],
            ['name' => 'Administrar terapeutas', 'slug' => 'therapists.manage', 'description' => 'Administrar perfiles, disponibilidad y bloqueos de terapeutas.'],
            ['name' => 'Administrar terapias', 'slug' => 'therapies.manage', 'description' => 'Administrar el catálogo configurable de terapias.'],
            ['name' => 'Ver pacientes', 'slug' => 'patients.view', 'description' => 'Consultar registros administrativos de pacientes y responsables.'],
            ['name' => 'Administrar pacientes', 'slug' => 'patients.manage', 'description' => 'Crear y modificar registros administrativos de pacientes y responsables.'],
            ['name' => 'Ver expediente clínico', 'slug' => 'clinical_records.view', 'description' => 'Consultar la información clínica base de los pacientes.'],
            ['name' => 'Administrar expediente clínico', 'slug' => 'clinical_records.manage', 'description' => 'Crear y modificar la información clínica base de los pacientes.'],
            ['name' => 'Ver agenda clínica', 'slug' => 'appointments.view', 'description' => 'Consultar la agenda y las citas clínicas.'],
            ['name' => 'Administrar agenda clínica', 'slug' => 'appointments.manage', 'description' => 'Crear, reprogramar y cancelar citas clínicas.'],
            ['name' => 'Ver bitácoras clínicas asignadas', 'slug' => 'session_logs.view', 'description' => 'Consultar bitácoras de sesiones clínicas autorizadas.'],
            ['name' => 'Capturar bitácoras clínicas asignadas', 'slug' => 'session_logs.manage', 'description' => 'Crear y actualizar bitácoras de sesiones clínicas autorizadas.'],
            ['name' => 'Administrar todas las bitácoras clínicas', 'slug' => 'session_logs.manage_all', 'description' => 'Consultar y administrar bitácoras clínicas sin restricción por asignación de terapeuta.'],
        ])->mapWithKeys(function (array $permission): array {
            $model = Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                $permission,
            );

            return [$permission['slug'] => $model];
        });

        $roles = [
            'administrator' => 'Administrador',
            'clinical_coordination' => 'Coordinación Clínica',
            'therapist' => 'Terapeuta',
            'reception' => 'Recepción',
            'consultation_direction' => 'Consulta / Dirección',
        ];

        foreach ($roles as $slug => $name) {
            Role::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => 'Rol base del sistema URPE Gestión Clínica.',
                    'is_system' => true,
                ],
            );
        }

        $administrator = Role::query()->where('slug', 'administrator')->firstOrFail();
        $administrator->permissions()->sync($permissions->pluck('id')->all());

        $clinicalCoordination = Role::query()->where('slug', 'clinical_coordination')->firstOrFail();
        $coordinationPermissionIds = $clinicalCoordination->permissions()->pluck('permissions.id')->all();
        foreach ([
            'therapies.manage', 'patients.view', 'patients.manage', 'clinical_records.view', 'clinical_records.manage',
            'appointments.view', 'appointments.manage', 'session_logs.view', 'session_logs.manage', 'session_logs.manage_all',
        ] as $slug) {
            $coordinationPermissionIds[] = $permissions->get($slug)->id;
        }
        $clinicalCoordination->permissions()->sync(array_values(array_unique($coordinationPermissionIds)));

        $therapist = Role::query()->where('slug', 'therapist')->firstOrFail();
        $therapistPermissionIds = $therapist->permissions()->pluck('permissions.id')->all();
        foreach (['session_logs.view', 'session_logs.manage'] as $slug) {
            $therapistPermissionIds[] = $permissions->get($slug)->id;
        }
        $therapist->permissions()->sync(array_values(array_unique($therapistPermissionIds)));

        $reception = Role::query()->where('slug', 'reception')->firstOrFail();
        $receptionPermissionIds = $reception->permissions()->pluck('permissions.id')->all();
        foreach (['patients.view', 'patients.manage', 'appointments.view', 'appointments.manage'] as $slug) {
            $receptionPermissionIds[] = $permissions->get($slug)->id;
        }
        $reception->permissions()->sync(array_values(array_unique($receptionPermissionIds)));

        $consultationDirection = Role::query()->where('slug', 'consultation_direction')->firstOrFail();
        $directionPermissionIds = $consultationDirection->permissions()->pluck('permissions.id')->all();
        foreach (['patients.view', 'appointments.view'] as $slug) {
            $directionPermissionIds[] = $permissions->get($slug)->id;
        }
        $consultationDirection->permissions()->sync(array_values(array_unique($directionPermissionIds)));
    }
}
