<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingTopic extends Model
{
    protected $fillable = [
        'meeting_id',
        'position',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'encrypted',
        ];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function getPositionWordAttribute(): string
    {
        $words = ['ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN'];

        return $words[$this->position - 1] ?? (string) $this->position;
    }
}
