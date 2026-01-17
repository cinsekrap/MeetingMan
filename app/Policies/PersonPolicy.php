<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Person;
use App\Models\User;

class PersonPolicy
{
    public function view(User $user, Person $person): bool
    {
        // Owner can always view
        if ($user->id === $person->user_id) {
            return true;
        }

        // Company admins can view people in their company
        if ($person->company_id) {
            $company = Company::find($person->company_id);
            if ($company && $user->isCompanyAdmin($company)) {
                return true;
            }
        }

        // Person is linked to this user (they can see meetings about themselves)
        if ($person->linked_user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function update(User $user, Person $person): bool
    {
        // Owner can always update
        if ($user->id === $person->user_id) {
            return true;
        }

        // Company admins can update people in their company
        if ($person->company_id) {
            $company = Company::find($person->company_id);
            if ($company && $user->isCompanyAdmin($company)) {
                return true;
            }
        }

        return false;
    }

    public function delete(User $user, Person $person): bool
    {
        // Owner can always delete
        if ($user->id === $person->user_id) {
            return true;
        }

        // Company owners can delete people in their company
        if ($person->company_id) {
            $company = Company::find($person->company_id);
            if ($company && $user->isCompanyOwner($company)) {
                return true;
            }
        }

        return false;
    }
}
