<?php

namespace App\Http\Controllers;

use App\Enums\CompanyRole;
use App\Mail\CompanyInvitation;
use App\Models\CompanyInvite;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyInviteController extends Controller
{
    public function index(): View
    {
        $company = auth()->user()->currentCompany();

        if (!$company) {
            return redirect()->route('company.setup');
        }

        // Check if user can manage invites
        if (!auth()->user()->isCompanyAdmin($company)) {
            abort(403, 'You do not have permission to manage invites.');
        }

        $invites = $company->invites()
            ->with('invitedBy')
            ->orderByDesc('created_at')
            ->get();

        // Get pending invite emails
        $pendingEmails = $company->invites()->pending()->pluck('email')->toArray();

        // Get people without accounts and without pending invites, with hierarchy
        $uninvitedPeople = $this->getUninvitedPeopleWithHierarchy($company, $pendingEmails);

        return view('company.invites.index', compact('company', 'invites', 'uninvitedPeople'));
    }

    /**
     * Get people without linked users, organized by hierarchy.
     */
    protected function getUninvitedPeopleWithHierarchy($company, array $pendingEmails): \Illuminate\Support\Collection
    {
        // Get top-level people (no reports_to)
        $topLevel = Person::where('company_id', $company->id)
            ->whereNull('reports_to_person_id')
            ->whereNull('linked_user_id')
            ->where(function ($q) use ($pendingEmails) {
                $q->whereNull('email')
                  ->orWhereNotIn('email', $pendingEmails);
            })
            ->active()
            ->orderBy('name')
            ->get();

        $result = collect();

        foreach ($topLevel as $person) {
            $person->hierarchy_level = 0;
            $result->push($person);
            $this->addUninvitedDescendants($person, $result, 1, $pendingEmails);
        }

        return $result;
    }

    /**
     * Recursively add uninvited descendants.
     */
    protected function addUninvitedDescendants(Person $person, \Illuminate\Support\Collection $collection, int $level, array $pendingEmails): void
    {
        $descendants = Person::where('reports_to_person_id', $person->id)
            ->whereNull('linked_user_id')
            ->where(function ($q) use ($pendingEmails) {
                $q->whereNull('email')
                  ->orWhereNotIn('email', $pendingEmails);
            })
            ->active()
            ->orderBy('name')
            ->get();

        foreach ($descendants as $descendant) {
            $descendant->hierarchy_level = $level;
            $collection->push($descendant);

            if ($level < 10) {
                $this->addUninvitedDescendants($descendant, $collection, $level + 1, $pendingEmails);
            }
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $company = auth()->user()->currentCompany();

        if (!$company) {
            return redirect()->route('company.setup');
        }

        // Check if user can manage invites
        if (!auth()->user()->isCompanyAdmin($company)) {
            abort(403, 'You do not have permission to send invites.');
        }

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                // Ensure not already a member
                function ($attribute, $value, $fail) use ($company) {
                    if ($company->users()->where('email', $value)->exists()) {
                        $fail('This user is already a member of this company.');
                    }
                },
                // Ensure no pending invite
                function ($attribute, $value, $fail) use ($company) {
                    if ($company->invites()->pending()->where('email', $value)->exists()) {
                        $fail('A pending invite already exists for this email.');
                    }
                },
            ],
            'role' => ['required', Rule::enum(CompanyRole::class)],
        ]);

        // Owners can invite any role, admins can only invite members
        if (!auth()->user()->isCompanyOwner($company) && $validated['role'] !== CompanyRole::Member->value) {
            return back()->with('error', 'Only company owners can invite admins or owners.');
        }

        $invite = $company->invites()->create([
            'email' => $validated['email'],
            'token' => Str::random(64),
            'role' => $validated['role'],
            'invited_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        // Send invite email
        Mail::to($invite->email)->send(new CompanyInvitation($invite));

        return redirect()->route('company.invites.index')
            ->with('success', "Invitation sent to {$invite->email}");
    }

    public function destroy(CompanyInvite $invite): RedirectResponse
    {
        $company = auth()->user()->currentCompany();

        if (!$company || $invite->company_id !== $company->id) {
            abort(403);
        }

        if (!auth()->user()->isCompanyAdmin($company)) {
            abort(403, 'You do not have permission to manage invites.');
        }

        $invite->delete();

        return redirect()->route('company.invites.index')
            ->with('success', 'Invitation cancelled.');
    }

    public function resend(CompanyInvite $invite): RedirectResponse
    {
        $company = auth()->user()->currentCompany();

        if (!$company || $invite->company_id !== $company->id) {
            abort(403);
        }

        if (!auth()->user()->isCompanyAdmin($company)) {
            abort(403, 'You do not have permission to manage invites.');
        }

        if (!$invite->isPending()) {
            return back()->with('error', 'This invite is no longer pending.');
        }

        // Extend expiration and resend
        $invite->update(['expires_at' => now()->addDays(7)]);

        Mail::to($invite->email)->send(new CompanyInvitation($invite));

        return redirect()->route('company.invites.index')
            ->with('success', "Invitation resent to {$invite->email}");
    }
}
