<?php

namespace App\Http\Controllers;

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\CompanyInvite;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanySetupController extends Controller
{
    public function show(Request $request)
    {
        $inviteToken = $request->query('invite');
        $invite = null;

        if ($inviteToken) {
            $invite = CompanyInvite::with('company')
                ->where('token', $inviteToken)
                ->pending()
                ->first();
        }

        return view('company.setup', compact('invite'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'action' => 'required|in:create,join',
            'company_name' => 'required_if:action,create|string|max:255',
            'invite_token' => 'required_if:action,join|string',
        ]);

        $user = $request->user();

        if ($request->action === 'create') {
            return $this->createCompany($user, $request->company_name);
        }

        return $this->joinViaInvite($user, $request->invite_token);
    }

    protected function createCompany($user, string $companyName)
    {
        DB::transaction(function () use ($user, $companyName) {
            $company = Company::create(['name' => $companyName]);

            $company->users()->attach($user->id, ['role' => CompanyRole::Owner->value]);

            // Migrate existing people to the new company
            $user->people()->whereNull('company_id')->update(['company_id' => $company->id]);

            $user->setCurrentCompany($company);
        });

        return redirect()->route('dashboard')->with('success', 'Company created successfully!');
    }

    protected function joinViaInvite($user, string $token)
    {
        $invite = CompanyInvite::with('company')
            ->where('token', $token)
            ->pending()
            ->first();

        if (!$invite) {
            return back()->with('error', 'Invalid or expired invite.');
        }

        // Check if email matches
        if (strtolower($invite->email) !== strtolower($user->email)) {
            return back()->with('error', 'This invite was sent to a different email address.');
        }

        DB::transaction(function () use ($user, $invite) {
            // Add user to company
            $invite->company->users()->attach($user->id, ['role' => $invite->role]);

            // Mark invite as accepted
            $invite->markAsAccepted();

            // Check if there's an existing Person record with this email to link
            $existingPerson = Person::where('company_id', $invite->company_id)
                ->where('email', $user->email)
                ->whereNull('linked_user_id')
                ->first();

            if ($existingPerson) {
                $existingPerson->update(['linked_user_id' => $user->id]);
            }

            $user->setCurrentCompany($invite->company);
        });

        return redirect()->route('dashboard')
            ->with('success', "You've joined {$invite->company->name}!");
    }
}
