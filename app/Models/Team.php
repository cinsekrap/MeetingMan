<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    protected $fillable = [
        'company_id',
        'name',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_lead')
            ->withTimestamps();
    }

    public function leads(): BelongsToMany
    {
        return $this->users()->wherePivot('is_lead', true);
    }

    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('is_lead', false);
    }

    public function isLead(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->wherePivot('is_lead', true)->exists();
    }

    public function isMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
}
