<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlannedTopic extends Model
{
    protected $fillable = [
        'person_id',
        'content',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
