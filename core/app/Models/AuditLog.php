<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    protected $fillable = [
        'previous_log_id', 'previous_hash', 'current_hash',
        'event_type', 'actor_type', 'actor_id', 'target_type', 'target_id',
        'payload', 'ip_address',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public static function appendEntry(string $eventType, array $payload = [], ?string $actorType = null, ?int $actorId = null, ?string $targetType = null, ?int $targetId = null): self
    {
        $last = self::orderBy('id', 'desc')->first();
        $previousHash = $last ? $last->current_hash : null;
        $payload['_nonce'] = $payload['_nonce'] ?? Str::random(16);
        $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $currentHash = hash('sha256', ($previousHash ?? '') . $eventType . $payloadJson);

        return self::create([
            'previous_log_id' => $last?->id,
            'previous_hash'   => $previousHash,
            'current_hash'    => $currentHash,
            'event_type'      => $eventType,
            'actor_type'      => $actorType,
            'actor_id'        => $actorId,
            'target_type'     => $targetType,
            'target_id'       => $targetId,
            'payload'         => $payload,
            'ip_address'      => request()->ip(),
        ]);
    }

    public static function verifyIntegrity(): array
    {
        $logs = self::orderBy('id')->get();
        $errors = [];
        $prevHash = null;
        foreach ($logs as $log) {
            $payload = $log->payload ?? [];
            $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
            $expected = hash('sha256', ($prevHash ?? '') . $log->event_type . $payloadJson);
            if ($log->current_hash !== $expected || ($prevHash !== null && $log->previous_hash !== $prevHash)) {
                $errors[] = ['log_id' => $log->id, 'expected' => $expected, 'got' => $log->current_hash];
            }
            $prevHash = $log->current_hash;
        }
        return $errors;
    }
}
