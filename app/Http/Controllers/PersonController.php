<?php

namespace App\Http\Controllers;

use App\Enums\CompanyRole;
use App\Mail\CompanyInvitation;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PersonController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $user = auth()->user();
        $company = $user->currentCompany();

        // Company admins see all people in the company, others see only their own
        if ($company && $user->isCompanyAdmin($company)) {
            $people = Person::where('company_id', $company->id)
                ->active()
                ->orderBy('name')
                ->get();
        } else {
            $people = $user->people()
                ->where('company_id', $company?->id)
                ->active()
                ->orderBy('name')
                ->get();
        }

        return view('people.index', compact('people'));
    }

    public function archived(): View
    {
        $user = auth()->user();
        $company = $user->currentCompany();

        // Company admins see all archived people in the company
        if ($company && $user->isCompanyAdmin($company)) {
            $people = Person::where('company_id', $company->id)
                ->archived()
                ->orderBy('name')
                ->get();
        } else {
            $people = $user->people()
                ->where('company_id', $company?->id)
                ->archived()
                ->orderBy('name')
                ->get();
        }

        return view('people.archived', compact('people'));
    }

    public function create(): View
    {
        $defaultFrequency = auth()->user()->getSettings()->default_meeting_frequency_days;
        $company = auth()->user()->currentCompany();

        // For reports_to dropdown, show all people in the company
        $allPeople = $company
            ? Person::where('company_id', $company->id)->active()->orderBy('name')->get()
            : collect();

        return view('people.create', compact('defaultFrequency', 'allPeople'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'meeting_frequency_days' => 'nullable|integer|min:1|max:365',
            'reports_to_person_id' => 'nullable|exists:people,id',
        ]);

        $company = auth()->user()->currentCompany();
        $sendInvite = $request->boolean('send_invite');
        $inviteSent = false;

        // Check if there's an existing user with this email in the company
        if (!empty($validated['email'])) {
            $existingUser = User::where('email', $validated['email'])->first();

            if ($existingUser && $company) {
                // Check if this user is in the same company
                if ($existingUser->companies()->where('companies.id', $company->id)->exists()) {
                    // Check if there's already a Person linked to this user
                    $existingPerson = Person::where('company_id', $company->id)
                        ->where('linked_user_id', $existingUser->id)
                        ->first();

                    if ($existingPerson) {
                        return back()
                            ->withInput()
                            ->with('error', "A person is already linked to the user with email {$validated['email']}.");
                    }

                    // Auto-link to existing user
                    $validated['linked_user_id'] = $existingUser->id;
                    $sendInvite = false; // Already a user, no need to invite
                }
            }
        }

        $validated['company_id'] = $company?->id;

        $person = auth()->user()->people()->create($validated);

        // Send invite if requested and user can send invites
        if ($sendInvite && $company && !empty($validated['email']) && auth()->user()->isCompanyAdmin($company)) {
            // Check no pending invite exists
            if (!$company->invites()->pending()->where('email', $validated['email'])->exists()) {
                $invite = $company->invites()->create([
                    'email' => $validated['email'],
                    'token' => Str::random(64),
                    'role' => CompanyRole::Member->value,
                    'invited_by' => auth()->id(),
                    'expires_at' => now()->addDays(7),
                ]);

                Mail::to($invite->email)->send(new CompanyInvitation($invite));
                $inviteSent = true;
            }
        }

        $message = 'Person added successfully.';
        if ($inviteSent) {
            $message .= ' Invitation sent to ' . $validated['email'] . '.';
        }

        return redirect()->route('people.index')
            ->with('success', $message);
    }

    public function edit(Person $person): View
    {
        $this->authorize('update', $person);

        $company = auth()->user()->currentCompany();

        // For reports_to dropdown, show all people in the company (excluding self)
        $allPeople = $company
            ? Person::where('company_id', $company->id)
                ->where('id', '!=', $person->id) // Exclude self
                ->active()
                ->orderBy('name')
                ->get()
            : collect();

        return view('people.edit', compact('person', 'allPeople'));
    }

    public function update(Request $request, Person $person): RedirectResponse
    {
        $this->authorize('update', $person);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'meeting_frequency_days' => 'nullable|integer|min:1|max:365',
            'reports_to_person_id' => 'nullable|exists:people,id',
        ]);

        // Prevent circular references
        if (!empty($validated['reports_to_person_id']) && $validated['reports_to_person_id'] == $person->id) {
            $validated['reports_to_person_id'] = null;
        }

        $person->update($validated);

        return redirect()->route('people.index')
            ->with('success', 'Person updated successfully.');
    }

    public function archive(Person $person): RedirectResponse
    {
        $this->authorize('update', $person);

        $person->update(['archived_at' => now()]);

        return redirect()->route('people.index')
            ->with('success', 'Person archived successfully.');
    }

    public function restore(Person $person): RedirectResponse
    {
        $this->authorize('update', $person);

        $person->update(['archived_at' => null]);

        return redirect()->route('people.archived')
            ->with('success', 'Person restored successfully.');
    }

    public function destroy(Person $person): RedirectResponse
    {
        $this->authorize('delete', $person);

        $person->delete();

        return redirect()->route('people.index')
            ->with('success', 'Person deleted successfully.');
    }
}
