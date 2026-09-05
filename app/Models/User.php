<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn ($query) => $query->where('slug', $permission))
            ->exists();
    }

    public function assignRole(Role|string $role): void
    {
        $roleModel = $role instanceof Role
            ? $role
            : Role::query()->where('slug', $role)->firstOrFail();

        $this->roles()->syncWithoutDetaching([$roleModel->getKey()]);
    }

    /**
     * @param  iterable<Role|string>  $roles
     */
    public function syncRoles(iterable $roles): void
    {
        $roleIds = collect($roles)
            ->map(function (Role|string $role): int {
                return $role instanceof Role
                    ? $role->getKey()
                    : Role::query()->where('slug', $role)->firstOrFail()->getKey();
            })
            ->unique()
            ->values()
            ->all();

        $this->roles()->sync($roleIds);
    }
}
