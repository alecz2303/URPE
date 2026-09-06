<?php

namespace App\Services;

use App\Models\AuditEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditTrail
{
    private const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'access_token',
        'refresh_token',
        'secret',
        'authorization',
        'cookie',
        'api_key',
        'apikey',
    ];

    public function record(
        string $event,
        ?Model $target = null,
        array $metadata = [],
        ?User $actor = null,
        ?Request $request = null,
    ): AuditEvent {
        $request ??= app()->bound('request') ? request() : null;

        if (! $actor && auth()->check() && auth()->user() instanceof User) {
            $actor = auth()->user();
        }

        return AuditEvent::query()->create([
            'actor_id' => $actor?->getKey(),
            'event' => $event,
            'target_type' => $target?->getMorphClass(),
            'target_id' => $target ? (string) $target->getKey() : null,
            'metadata' => $this->sanitize($metadata),
            'route_name' => $request?->route()?->getName(),
            'method' => $request?->method(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function sanitize(array $metadata): array
    {
        $clean = [];

        foreach ($metadata as $key => $value) {
            if (is_string($key) && $this->isSensitiveKey($key)) {
                continue;
            }

            $clean[$key] = is_array($value) ? $this->sanitize($value) : $value;
        }

        return $clean;
    }

    private function isSensitiveKey(string $key): bool
    {
        $normalized = strtolower(trim($key));

        foreach (self::SENSITIVE_KEYS as $sensitiveKey) {
            if ($normalized === $sensitiveKey || str_ends_with($normalized, '_'.$sensitiveKey)) {
                return true;
            }
        }

        return false;
    }
}
