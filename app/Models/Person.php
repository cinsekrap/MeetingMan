<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Person extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'linked_user_id',
        'reports_to_person_id',
        'name',
        'email',
        'meeting_frequency_days',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
            'meeting_frequency_days' => 'integer',
        ];
    }

    public function isMeetingOverdue(): bool
    {
        $frequency = $this->meeting_frequency_days;

        if (!$frequency) {
            return false;
        }

        $lastMeeting = $this->meetings()->latest('meeting_date')->first();

        if (!$lastMeeting) {
            return true; // No meetings yet, consider overdue if frequency is set
        }

        return $lastMeeting->meeting_date->addDays($frequency)->isPast();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function linkedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }

    public function isLinkedToUser(): bool
    {
        return $this->linked_user_id !== null;
    }

    public function reportsTo(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'reports_to_person_id');
    }

    public function directReports(): HasMany
    {
        return $this->hasMany(Person::class, 'reports_to_person_id');
    }

    /**
     * Get all descendants (direct reports and their reports, recursively).
     */
    public function getAllDescendants(): \Illuminate\Support\Collection
    {
        $descendants = collect();

        foreach ($this->directReports as $report) {
            $descendants->push($report);
            $descendants = $descendants->merge($report->getAllDescendants());
        }

        return $descendants;
    }

    /**
     * Get the hierarchy level (0 = top level, 1 = reports to someone, etc.)
     */
    public function getHierarchyLevel(): int
    {
        $level = 0;
        $current = $this;

        while ($current->reports_to_person_id) {
            $level++;
            $current = $current->reportsTo;
            if ($level > 10) break; // Prevent infinite loops
        }

        return $level;
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(Objective::class);
    }

    public function plannedTopics(): HasMany
    {
        return $this->hasMany(PlannedTopic::class);
    }

    public function actions(): HasManyThrough
    {
        return $this->hasManyThrough(Action::class, Meeting::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }
}
