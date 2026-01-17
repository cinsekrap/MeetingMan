<?php

namespace App\Http\Controllers;

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function switch(Company $company): RedirectResponse
    {
        $user = auth()->user();

        // Verify user belongs to this company
        if (!$user->companies()->where('companies.id', $company->id)->exists()) {
            abort(403, 'You do not belong to this company.');
        }

        $user->setCurrentCompany($company);

        return redirect()->back()->with('success', "Switched to {$company->name}");
    }

    public function create(): View
    {
        return view('company.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        DB::transaction(function () use ($user, $validated) {
            $company = Company::create(['name' => $validated['name']]);

            $company->users()->attach($user->id, ['role' => CompanyRole::Owner->value]);

            $user->setCurrentCompany($company);
        });

        return redirect()->route('dashboard')->with('success', 'Company created successfully!');
    }

    public function settings(): View
    {
        $company = auth()->user()->currentCompany();

        if (!$company) {
            return redirect()->route('company.setup');
        }

        // Check if user can manage company settings
        if (!auth()->user()->isCompanyAdmin($company)) {
            abort(403, 'You do not have permission to manage company settings.');
        }

        // Get all people in the company with hierarchy
        $people = $this->getPeopleWithHierarchy($company);

        // Get pending invites
        $pendingInvites = $company->invites()->pending()->orderByDesc('created_at')->get();

        return view('company.settings', compact('company', 'people', 'pendingInvites'));
    }

    /**
     * Get all people in a company organized by hierarchy.
     */
    protected function getPeopleWithHierarchy(Company $company): \Illuminate\Support\Collection
    {
        // Get top-level people (no reports_to)
        $topLevel = Person::where('company_id', $company->id)
            ->whereNull('reports_to_person_id')
            ->active()
            ->orderBy('name')
            ->get();

        $result = collect();

        foreach ($topLevel as $person) {
            $person->hierarchy_level = 0;
            $result->push($person);
            $this->addDescendants($person, $result, 1);
        }

        return $result;
    }

    /**
     * Recursively add descendants to the collection.
     */
    protected function addDescendants(Person $person, \Illuminate\Support\Collection $collection, int $level): void
    {
        $descendants = $person->directReports()->active()->orderBy('name')->get();

        foreach ($descendants as $descendant) {
            $descendant->hierarchy_level = $level;
            $collection->push($descendant);

            if ($level < 10) {
                $this->addDescendants($descendant, $collection, $level + 1);
            }
        }
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $company = auth()->user()->currentCompany();

        if (!$company) {
            return redirect()->route('company.setup');
        }

        // Check if user can manage company settings
        if (!auth()->user()->isCompanyAdmin($company)) {
            abort(403, 'You do not have permission to manage company settings.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $company->update($validated);

        return redirect()->route('company.settings')
            ->with('success', 'Company settings updated successfully.');
    }
}
