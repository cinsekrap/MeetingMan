<?php

namespace App\Http\Controllers;

use App\Enums\ActionStatus;
use App\Mail\ActionReminder;
use App\Mail\ActionReminderBatch;
use App\Models\Action;
use App\Models\Person;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ActionController extends Controller
{
    use AuthorizesRequests;

    public function globalIndex(Request $request): View
    {
        $filter = $request->get('filter');

        $query = Action::whereHas('meeting.person', function ($q) {
            $q->where('user_id', auth()->id())->whereNull('archived_at');
        })->with(['meeting.person']);

        $title = 'All Actions';

        if ($filter === 'overdue') {
            $query->overdue();
            $title = 'Overdue Actions';
        } elseif ($filter === 'due_soon') {
            $query->dueSoon();
            $title = 'Actions Due Soon';
        } elseif ($filter === 'pending') {
            $query->pending();
            $title = 'Pending Actions';
        }

        $actions = $query
            ->orderByRaw("CASE WHEN status IN ('complete', 'dropped') THEN 1 ELSE 0 END")
            ->orderBy('due_date')
            ->get()
            ->groupBy('meeting.person.name');

        $statuses = ActionStatus::cases();

        return view('actions.global', compact('actions', 'statuses', 'title', 'filter'));
    }

    public function index(Person $person): View
    {
        $this->authorize('view', $person);

        $actions = $person->actions()
            ->with('meeting')
            ->orderByRaw("CASE WHEN status IN ('complete', 'dropped') THEN 1 ELSE 0 END")
            ->orderBy('due_date')
            ->get();

        $statuses = ActionStatus::cases();

        return view('actions.index', compact('person', 'actions', 'statuses'));
    }

    public function updateStatus(Request $request, Action $action): JsonResponse
    {
        // Check authorization through the meeting's person
        if ($action->meeting->person->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:not_started,on_track,complete,dropped',
        ]);

        $action->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'status' => $action->status->value,
            'label' => $action->status->label(),
        ]);
    }

    public function sendReminder(Request $request, Action $action): RedirectResponse
    {
        // Check authorization through the meeting's person
        $person = $action->meeting->person;
        if ($person->user_id !== auth()->id()) {
            abort(403);
        }

        // Check person has an email
        if (!$person->email) {
            return redirect()->back()
                ->with('error', "Cannot send reminder: {$person->name} has no email address.");
        }

        $validated = $request->validate([
            'type' => 'required|in:overdue,due_soon',
        ]);

        Mail::to($person->email)->send(new ActionReminder(
            $action,
            auth()->user(),
            $validated['type'],
        ));

        return redirect()->back()
            ->with('success', "Reminder sent to {$person->name}.");
    }

    public function sendBatchReminder(Person $person): RedirectResponse
    {
        $this->authorize('view', $person);

        // Check person has an email
        if (!$person->email) {
            return redirect()->back()
                ->with('error', "Cannot send reminder: {$person->name} has no email address.");
        }

        // Get overdue and due soon actions
        $overdueActions = $person->actions()->overdue()->with('meeting')->get();
        $dueSoonActions = $person->actions()->dueSoon()->with('meeting')->get();

        if ($overdueActions->isEmpty() && $dueSoonActions->isEmpty()) {
            return redirect()->back()
                ->with('error', "No overdue or due soon actions to send reminders for.");
        }

        Mail::to($person->email)->send(new ActionReminderBatch(
            $overdueActions,
            $dueSoonActions,
            auth()->user(),
            $person->name,
        ));

        $count = $overdueActions->count() + $dueSoonActions->count();

        return redirect()->back()
            ->with('success', "Reminder for {$count} action(s) sent to {$person->name}.");
    }
}
