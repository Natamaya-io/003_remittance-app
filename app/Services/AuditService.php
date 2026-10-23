<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\App;
use App\Core\Auth;

final class AuditService
{
    public static function log(string $action, string $entityType, ?int $entityId = null, ?array $before = null, ?array $after = null): void
    {
        try {
            $stmt = App::db()->prepare(
                'INSERT INTO audit_logs (user_id, action, entity_type, entity_id, before_data, after_data, ip_address, user_agent, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())'
            );
            $stmt->execute([
                Auth::id(),
                $action,
                $entityType,
                $entityId,
                $before ? json_encode($before, JSON_UNESCAPED_UNICODE) : null,
                $after ? json_encode($after, JSON_UNESCAPED_UNICODE) : null,
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
            ]);
        } catch (\Throwable) {
            // Audit logging must not block the main transaction.
        }
    }
}
