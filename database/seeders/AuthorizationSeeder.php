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
    }
}
