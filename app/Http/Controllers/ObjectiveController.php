<?php

namespace App\Http\Controllers;

use App\Enums\ObjectiveStatus;
use App\Models\Objective;
use App\Models\Person;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObjectiveController extends Controller
{
    use AuthorizesRequests;

    public function globalIndex(Request $request): View
    {
        $filter = $request->get('filter');

        $query = Objective::whereHas('person', function ($q) {
            $q->where('user_id', auth()->id())->whereNull('archived_at');
        })->with('person');

        $title = 'All Objectives';

        if ($filter === 'off_track') {
            $query->active()->where('status', ObjectiveStatus::OffTrack);
            $title = 'Off-Track Objectives';
        } elseif ($filter === 'active') {
            $query->active();
            $title = 'Active Objectives';
        }

        $objectives = $query
            ->orderByRaw("CASE WHEN status IN ('complete', 'dropped') THEN 1 ELSE 0 END")
            ->orderBy('due_date')
            ->get()
            ->groupBy('person.name');

        $statuses = ObjectiveStatus::cases();

        return view('objectives.global', compact('objectives', 'statuses', 'title', 'filter'));
    }

    public function index(Person $person): View
    {
        $this->authorize('view', $person);

        $objectives = $person->objectives()->orderBy('due_date')->get();
        $statuses = ObjectiveStatus::cases();

        return view('objectives.index', compact('person', 'objectives', 'statuses'));
    }

    public function store(Request $request, Person $person): RedirectResponse
    {
        $this->authorize('view', $person);

        $validated = $request->validate([
            'definition' => 'required|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
        ]);

        $person->objectives()->create($validated);

        return redirect()->route('people.objectives.index', $person)
            ->with('success', 'Objective added successfully.');
    }

    public function update(Request $request, Objective $objective): RedirectResponse
    {
        $this->authorize('update', $objective);

        $validated = $request->validate([
            'definition' => 'required|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:on_track,off_track,complete,dropped',
        ]);

        $objective->update($validated);

        return redirect()->route('people.objectives.index', $objective->person)
            ->with('success', 'Objective updated successfully.');
    }

    public function destroy(Objective $objective): RedirectResponse
    {
        $this->authorize('delete', $objective);

        $person = $objective->person;
        $objective->delete();

        return redirect()->route('people.objectives.index', $person)
            ->with('success', 'Objective deleted successfully.');
    }

    public function updateStatus(Request $request, Objective $objective): JsonResponse
    {
        if ($objective->person->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:on_track,off_track,complete,dropped',
        ]);

        $objective->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'status' => $objective->status->value,
            'label' => $objective->status->label(),
        ]);
    }
}
