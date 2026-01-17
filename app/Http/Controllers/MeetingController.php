<?php

namespace App\Http\Controllers;

use App\Mail\MeetingSummary;
use App\Models\Meeting;
use App\Models\Person;
use App\Models\PlannedTopic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MeetingController extends Controller
{
    use AuthorizesRequests;

    public function index(Person $person): View
    {
        $this->authorize('view', $person);

        $meetings = $person->meetings()->orderByDesc('meeting_date')->get();
        $plannedTopics = $person->plannedTopics()->orderBy('created_at')->get();

        return view('meetings.index', compact('person', 'meetings', 'plannedTopics'));
    }

    public function create(Person $person): View
    {
        $this->authorize('view', $person);

        $company = auth()->user()->currentCompany();
        $people = auth()->user()->allPeopleIncludingSkipLevels($company?->id);
        $plannedTopics = $person->plannedTopics()->orderBy('created_at')->get();

        return view('meetings.create', compact('person', 'people', 'plannedTopics'));
    }

    public function store(Request $request, Person $person): RedirectResponse
    {
        $this->authorize('view', $person);

        $validated = $request->validate([
            'meeting_date' => 'required|date',
            'mood' => 'required|integer|min:1|max:5',
            'planned_topic_ids' => 'nullable|array',
            'planned_topic_ids.*' => 'exists:planned_topics,id',
            'topics' => 'nullable|array',
            'topics.*.content' => 'nullable|string',
            'actions' => 'nullable|array',
            'actions.*.description' => 'nullable|string',
            'actions.*.assigned_to_person_id' => 'nullable|exists:people,id',
            'actions.*.assigned_to_text' => 'nullable|string|max:255',
            'actions.*.due_date' => 'nullable|date',
        ]);

        $meeting = $person->meetings()->create([
            'meeting_date' => $validated['meeting_date'],
            'mood' => $validated['mood'],
            'shared_with_person' => $request->boolean('shared_with_person'),
        ]);

        // Create topics
        $position = 1;
        if (!empty($validated['topics'])) {
            foreach ($validated['topics'] as $topic) {
                if (!empty($topic['content'])) {
                    $meeting->topics()->create([
                        'position' => $position++,
                        'content' => $topic['content'],
                    ]);
                }
            }
        }

        // Delete used planned topics
        if (!empty($validated['planned_topic_ids'])) {
            PlannedTopic::whereIn('id', $validated['planned_topic_ids'])
                ->where('person_id', $person->id)
                ->delete();
        }

        // Create actions
        if (!empty($validated['actions'])) {
            foreach ($validated['actions'] as $action) {
                if (!empty($action['description'])) {
                    $meeting->actions()->create([
                        'description' => $action['description'],
                        'assigned_to_person_id' => $action['assigned_to_person_id'] ?: null,
                        'assigned_to_text' => $action['assigned_to_text'] ?: null,
                        'due_date' => $action['due_date'] ?? now()->addWeek(),
                        'status' => 'not_started',
                    ]);
                }
            }
        }

        // Send email if requested
        $emailSent = false;
        if ($request->boolean('send_email') && $person->email) {
            $meeting->load(['person', 'topics', 'actions.assignedToPerson']);
            $overdueActions = $person->actions()->overdue()->with('meeting')->get();
            $dueSoonActions = $person->actions()->dueSoon()->with('meeting')->get();

            Mail::to($person->email)->send(new MeetingSummary(
                $meeting,
                auth()->user(),
                $overdueActions,
                $dueSoonActions,
            ));
            $emailSent = true;
        }

        $message = 'Meeting recorded successfully.';
        if ($emailSent) {
            $message .= ' Summary email sent to ' . $person->email . '.';
        }

        return redirect()->route('meetings.show', $meeting)
            ->with('success', $message);
    }

    public function show(Meeting $meeting): View
    {
        $this->authorize('view', $meeting);

        $meeting->load(['topics', 'actions.assignedToPerson']);

        return view('meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting): View
    {
        $this->authorize('update', $meeting);

        $meeting->load(['topics', 'actions']);
        $person = $meeting->person;
        $company = auth()->user()->currentCompany();
        $people = auth()->user()->allPeopleIncludingSkipLevels($company?->id);

        return view('meetings.edit', compact('meeting', 'person', 'people'));
    }

    public function update(Request $request, Meeting $meeting): RedirectResponse
    {
        $this->authorize('update', $meeting);

        $validated = $request->validate([
            'meeting_date' => 'required|date',
            'mood' => 'required|integer|min:1|max:5',
            'topics' => 'nullable|array',
            'topics.*.content' => 'nullable|string',
            'actions' => 'nullable|array',
            'actions.*.id' => 'nullable|exists:actions,id',
            'actions.*.description' => 'nullable|string',
            'actions.*.assigned_to_person_id' => 'nullable|exists:people,id',
            'actions.*.assigned_to_text' => 'nullable|string|max:255',
            'actions.*.due_date' => 'nullable|date',
            'actions.*.status' => 'nullable|in:not_started,on_track,complete,dropped',
        ]);

        $meeting->update([
            'meeting_date' => $validated['meeting_date'],
            'mood' => $validated['mood'],
            'shared_with_person' => $request->boolean('shared_with_person'),
        ]);

        // Replace topics
        $meeting->topics()->delete();
        if (!empty($validated['topics'])) {
            $position = 1;
            foreach ($validated['topics'] as $topic) {
                if (!empty($topic['content'])) {
                    $meeting->topics()->create([
                        'position' => $position++,
                        'content' => $topic['content'],
                    ]);
                }
            }
        }

        // Replace actions
        $meeting->actions()->delete();
        if (!empty($validated['actions'])) {
            foreach ($validated['actions'] as $action) {
                if (!empty($action['description'])) {
                    $meeting->actions()->create([
                        'description' => $action['description'],
                        'assigned_to_person_id' => $action['assigned_to_person_id'] ?: null,
                        'assigned_to_text' => $action['assigned_to_text'] ?: null,
                        'due_date' => $action['due_date'] ?? now()->addWeek(),
                        'status' => $action['status'] ?? 'not_started',
                    ]);
                }
            }
        }

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting updated successfully.');
    }

    public function destroy(Meeting $meeting): RedirectResponse
    {
        $this->authorize('delete', $meeting);

        $person = $meeting->person;
        $meeting->delete();

        return redirect()->route('people.meetings.index', $person)
            ->with('success', 'Meeting deleted successfully.');
    }

    public function email(Meeting $meeting): View
    {
        $this->authorize('view', $meeting);

        $meeting->load(['person', 'topics', 'actions.assignedToPerson']);

        // Get overdue and due soon actions for this person (not just this meeting)
        $overdueActions = $meeting->person->actions()->overdue()->with('meeting')->get();
        $dueSoonActions = $meeting->person->actions()->dueSoon()->with('meeting')->get();

        return view('meetings.email', compact('meeting', 'overdueActions', 'dueSoonActions'));
    }
}
