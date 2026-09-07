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
        $coordinationPermissionIds[] = $permissions->get('therapies.manage')->id;
        $coordinationPermissionIds[] = $permissions->get('patients.view')->id;
        $coordinationPermissionIds[] = $permissions->get('patients.manage')->id;
        $coordinationPermissionIds[] = $permissions->get('clinical_records.view')->id;
        $coordinationPermissionIds[] = $permissions->get('clinical_records.manage')->id;
        $clinicalCoordination->permissions()->sync(array_values(array_unique($coordinationPermissionIds)));

        $reception = Role::query()->where('slug', 'reception')->firstOrFail();
        $receptionPermissionIds = $reception->permissions()->pluck('permissions.id')->all();
        $receptionPermissionIds[] = $permissions->get('patients.view')->id;
        $receptionPermissionIds[] = $permissions->get('patients.manage')->id;
        $reception->permissions()->sync(array_values(array_unique($receptionPermissionIds)));

        $consultationDirection = Role::query()->where('slug', 'consultation_direction')->firstOrFail();
        $directionPermissionIds = $consultationDirection->permissions()->pluck('permissions.id')->all();
        $directionPermissionIds[] = $permissions->get('patients.view')->id;
        $consultationDirection->permissions()->sync(array_values(array_unique($directionPermissionIds)));
    }
}
