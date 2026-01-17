<?php

namespace App\Models;

use App\Enums\ObjectiveStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Objective extends Model
{
    protected $fillable = [
        'person_id',
        'definition',
        'start_date',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'definition' => 'encrypted',
            'start_date' => 'date',
            'due_date' => 'date',
            'status' => ObjectiveStatus::class,
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [ObjectiveStatus::Complete, ObjectiveStatus::Dropped]);
    }

    public function scopeRelevantForMeeting(Builder $query): Builder
    {
        return $query->active()
            ->where('start_date', '<=', now()->addDays(5));
    }
}
