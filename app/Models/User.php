<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\CompanyRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('is_lead')
            ->withTimestamps();
    }

    public function currentCompany(): ?Company
    {
        $companyId = session('current_company_id');

        if ($companyId) {
            $company = $this->companies()->where('companies.id', $companyId)->first();
            if ($company) {
                return $company;
            }
        }

        // Default to first company if no current set or invalid
        $company = $this->companies()->first();
        if ($company) {
            session(['current_company_id' => $company->id]);
        }

        return $company;
    }

    public function setCurrentCompany(Company $company): void
    {
        if ($this->companies()->where('companies.id', $company->id)->exists()) {
            session(['current_company_id' => $company->id]);
        }
    }

    public function needsCompanySetup(): bool
    {
        return $this->companies()->count() === 0;
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    /**
     * Get all people including skip-levels (direct reports' descendants).
     * Returns a collection with hierarchy info.
     */
    public function allPeopleIncludingSkipLevels(?int $companyId = null): \Illuminate\Support\Collection
    {
        $query = $this->people()->active();

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $directReports = $query->orderBy('name')->get();
        $allPeople = collect();

        foreach ($directReports as $person) {
            // Add direct report with level 0
            $person->hierarchy_level = 0;
            $person->hierarchy_prefix = '';
            $allPeople->push($person);

            // Add their descendants (skip-levels) with increasing levels
            $this->addDescendantsWithLevel($person, $allPeople, 1);
        }

        return $allPeople;
    }

    /**
     * Recursively add descendants with their hierarchy level.
     */
    protected function addDescendantsWithLevel(Person $person, \Illuminate\Support\Collection $collection, int $level): void
    {
        foreach ($person->directReports()->active()->orderBy('name')->get() as $descendant) {
            $descendant->hierarchy_level = $level;
            $descendant->hierarchy_prefix = str_repeat('— ', $level);
            $collection->push($descendant);

            if ($level < 5) { // Limit depth to prevent infinite loops
                $this->addDescendantsWithLevel($descendant, $collection, $level + 1);
            }
        }
    }

    public function meetings(): HasManyThrough
    {
        return $this->hasManyThrough(Meeting::class, Person::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    public function getSettings(): UserSetting
    {
        return $this->settings ?? $this->settings()->create([
            'default_meeting_frequency_days' => 14,
        ]);
    }

    public function getRoleInCompany(Company $company): ?CompanyRole
    {
        $pivot = $this->companies()->where('companies.id', $company->id)->first()?->pivot;
        return $pivot ? CompanyRole::from($pivot->role) : null;
    }

    public function isCompanyAdmin(?Company $company = null): bool
    {
        $company = $company ?? $this->currentCompany();
        if (!$company) {
            return false;
        }
        $role = $this->getRoleInCompany($company);
        return $role && $role->canManageCompany();
    }

    public function isCompanyOwner(?Company $company = null): bool
    {
        $company = $company ?? $this->currentCompany();
        if (!$company) {
            return false;
        }
        return $this->getRoleInCompany($company) === CompanyRole::Owner;
    }

    public function linkedPersonRecords(): HasMany
    {
        return $this->hasMany(Person::class, 'user_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'suspended_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'suspended_at' => 'datetime',
        ];
    }

    public function adminNotifications(): HasMany
    {
        return $this->hasMany(AdminNotification::class);
    }

    public function unreadAdminNotifications(): HasMany
    {
        return $this->adminNotifications()->unread();
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }
}
