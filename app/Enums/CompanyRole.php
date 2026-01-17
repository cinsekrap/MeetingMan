<?php

namespace App\Enums;

enum CompanyRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Admin => 'Admin',
            self::Member => 'Member',
        };
    }

    public function canManageCompany(): bool
    {
        return in_array($this, [self::Owner, self::Admin]);
    }

    public function canManageUsers(): bool
    {
        return in_array($this, [self::Owner, self::Admin]);
    }

    public function canAccessUserData(): bool
    {
        return in_array($this, [self::Owner, self::Admin]);
    }

    public function canDeleteCompany(): bool
    {
        return $this === self::Owner;
    }
}
