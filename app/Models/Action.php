<?php

namespace App\Models;

use App\Enums\ActionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Action extends Model
{
    protected $fillable = [
        'meeting_id',
        'description',
        'assigned_to_person_id',
        'assigned_to_text',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'description' => 'encrypted',
            'due_date' => 'date',
            'status' => ActionStatus::class,
        ];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function assignedToPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'assigned_to_person_id');
    }

    public function getAssignedToNameAttribute(): string
    {
        if ($this->assignedToPerson) {
            return $this->assignedToPerson->name;
        }

        return $this->assigned_to_text ?? 'Unassigned';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast()
            && ! in_array($this->status, [ActionStatus::Complete, ActionStatus::Dropped]);
    }

    public function getIsDueSoonAttribute(): bool
    {
        return $this->due_date->isBetween(now(), now()->addDays(3))
            && ! in_array($this->status, [ActionStatus::Complete, ActionStatus::Dropped]);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', [ActionStatus::Complete, ActionStatus::Dropped]);
    }

    public function scopeDueSoon(Builder $query): Builder
    {
        return $query->whereBetween('due_date', [now(), now()->addDays(3)])
            ->whereNotIn('status', [ActionStatus::Complete, ActionStatus::Dropped]);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNotIn('status', [ActionStatus::Complete, ActionStatus::Dropped]);
    }
}
