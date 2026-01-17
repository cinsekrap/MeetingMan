<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    protected $fillable = [
        'person_id',
        'meeting_date',
        'mood',
        'shared_with_person',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date' => 'datetime',
            'shared_with_person' => 'boolean',
        ];
    }

    public function isSharedWithPerson(): bool
    {
        return $this->shared_with_person && $this->person->isLinkedToUser();
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(MeetingTopic::class)->orderBy('position');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    public function getMoodEmojiAttribute(): string
    {
        return match ($this->mood) {
            1 => '😭',
            2 => '😞',
            3 => '😐',
            4 => '🙂',
            5 => '😀',
            default => '😐',
        };
    }
}
