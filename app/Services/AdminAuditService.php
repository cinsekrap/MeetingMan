<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AdminAuditService
{
    public function log(
        User $admin,
        string $action,
        ?User $targetUser = null,
        ?string $justification = null,
        ?array $metadata = null
    ): AdminAuditLog {
        $log = AdminAuditLog::create([
            'admin_id' => $admin->id,
            'target_user_id' => $targetUser?->id,
            'action' => $action,
            'justification' => $justification,
            'metadata' => $metadata,
            'ip_address' => Request::ip(),
        ]);

        // Create notification for target user if this is a data access action
        if ($targetUser && $this->shouldNotifyUser($action)) {
            $this->createNotification($targetUser, $log, $admin);
        }

        return $log;
    }

    protected function shouldNotifyUser(string $action): bool
    {
        return in_array($action, [
            'view_user_data',
            'suspend_user',
            'unsuspend_user',
            'force_password_reset',
        ]);
    }

    protected function createNotification(User $user, AdminAuditLog $log, User $admin): void
    {
        $message = match ($log->action) {
            'view_user_data' => "An administrator accessed your account data on {$log->created_at->format('M j, Y \\a\\t g:i A')}. Reason: {$log->justification}",
            'suspend_user' => "Your account was suspended by an administrator on {$log->created_at->format('M j, Y \\a\\t g:i A')}.",
            'unsuspend_user' => "Your account suspension was lifted on {$log->created_at->format('M j, Y \\a\\t g:i A')}.",
            'force_password_reset' => "A password reset was initiated for your account on {$log->created_at->format('M j, Y \\a\\t g:i A')}.",
            default => "Administrative action performed on your account on {$log->created_at->format('M j, Y \\a\\t g:i A')}.",
        };

        AdminNotification::create([
            'user_id' => $user->id,
            'audit_log_id' => $log->id,
            'message' => $message,
        ]);
    }
}
