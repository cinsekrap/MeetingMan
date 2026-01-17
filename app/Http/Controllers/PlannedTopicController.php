<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\PlannedTopic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlannedTopicController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Person $person): RedirectResponse
    {
        $this->authorize('view', $person);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $person->plannedTopics()->create($validated);

        return redirect()->back()
            ->with('success', "Topic added for next meeting with {$person->name}.");
    }

    public function destroy(PlannedTopic $plannedTopic): RedirectResponse
    {
        if ($plannedTopic->person->user_id !== auth()->id()) {
            abort(403);
        }

        $plannedTopic->delete();

        return redirect()->back()
            ->with('success', 'Planned topic removed.');
    }
}
