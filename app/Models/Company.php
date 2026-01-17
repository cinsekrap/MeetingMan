<?php

namespace App\Models;

use App\Enums\CompanyRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'logo_path',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(CompanyInvite::class);
    }

    public function owners(): BelongsToMany
    {
        return $this->users()->wherePivot('role', CompanyRole::Owner->value);
    }

    public function admins(): BelongsToMany
    {
        return $this->users()->wherePivot('role', CompanyRole::Admin->value);
    }

    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('role', CompanyRole::Member->value);
    }

    public function getUserRole(User $user): ?CompanyRole
    {
        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;
        return $pivot ? CompanyRole::from($pivot->role) : null;
    }

    public function isOwner(User $user): bool
    {
        return $this->getUserRole($user) === CompanyRole::Owner;
    }

    public function isAdmin(User $user): bool
    {
        $role = $this->getUserRole($user);
        return $role && $role->canManageCompany();
    }
}
