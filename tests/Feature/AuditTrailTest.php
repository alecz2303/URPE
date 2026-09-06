<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\User;
use App\Services\AuditTrail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_event_records_actor_target_metadata_and_request_context(): void
    {
        $actor = User::factory()->create();
        $target = User::factory()->create();
        $request = Request::create('/usuarios/'.$target->id, 'PUT', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'URPE-Test-Agent',
        ]);

        $event = app(AuditTrail::class)->record(
            'user.updated',
            $target,
            ['fields' => ['name', 'email']],
            $actor,
            $request,
        );

        $this->assertDatabaseHas('audit_events', [
            'id' => $event->id,
            'actor_id' => $actor->id,
            'event' => 'user.updated',
            'target_type' => $target->getMorphClass(),
            'target_id' => (string) $target->id,
            'method' => 'PUT',
            'ip_address' => '127.0.0.1',
        ]);

        $event->refresh();
        $this->assertSame(['fields' => ['name', 'email']], $event->metadata);
        $this->assertSame('URPE-Test-Agent', $event->user_agent);
    }

    public function test_sensitive_values_are_removed_recursively_from_audit_metadata(): void
    {
        $event = app(AuditTrail::class)->record('security.test', metadata: [
            'email' => 'admin@urpe.test',
            'password' => 'NeverStoreThis',
            'nested' => [
                'current_password' => 'NeverStoreThisEither',
                'safe' => 'visible',
                'api_key' => 'secret-key',
            ],
            'profile_token' => 'should-also-be-removed',
        ]);

        $this->assertSame([
            'email' => 'admin@urpe.test',
            'nested' => [
                'safe' => 'visible',
            ],
        ], $event->metadata);
    }

    public function test_deleting_actor_does_not_delete_audit_history(): void
    {
        $actor = User::factory()->create();
        $event = app(AuditTrail::class)->record('security.test', actor: $actor);

        $actor->delete();

        $this->assertDatabaseHas('audit_events', [
            'id' => $event->id,
            'actor_id' => null,
            'event' => 'security.test',
        ]);
        $this->assertInstanceOf(AuditEvent::class, $event->fresh());
    }
}
