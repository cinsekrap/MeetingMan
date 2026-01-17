<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAuditLog extends Model
{
    protected $fillable = [
        'admin_id',
        'target_user_id',
        'action',
        'justification',
        'metadata',
        'ip_address',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'view_user_data' => 'Viewed user data',
            'suspend_user' => 'Suspended user',
            'unsuspend_user' => 'Unsuspended user',
            'delete_user' => 'Deleted user',
            'force_password_reset' => 'Forced password reset',
            'promote_admin' => 'Promoted to admin',
            'demote_admin' => 'Demoted from admin',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }
}
