<?php

namespace App\Http\Controllers;

use App\Enums\ObjectiveStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $company = auth()->user()->currentCompany();

        $directReports = auth()->user()->people()
            ->where('company_id', $company?->id)
            ->whereNull('reports_to_person_id') // Only direct reports, not skip-levels
            ->active()
            ->withCount([
                'meetings',
                'actions as pending_actions_count' => fn($q) => $q->pending(),
                'actions as overdue_actions_count' => fn($q) => $q->overdue(),
                'actions as due_soon_actions_count' => fn($q) => $q->dueSoon(),
                'objectives as active_objectives_count' => fn($q) => $q->active(),
                'objectives as off_track_objectives_count' => fn($q) => $q->active()->where('status', ObjectiveStatus::OffTrack),
            ])
            ->with(['meetings' => fn($q) => $q->latest('meeting_date')->take(3)])
            ->get();

        // Sort: needs attention first (overdue actions, off-track objectives, or overdue meetings), then alphabetically
        $sorted = $directReports->sortBy([
            fn($a, $b) => ($b->overdue_actions_count > 0 || $b->off_track_objectives_count > 0 || $b->isMeetingOverdue()) <=> ($a->overdue_actions_count > 0 || $a->off_track_objectives_count > 0 || $a->isMeetingOverdue()),
            fn($a, $b) => $a->name <=> $b->name,
        ]);

        $totalPeopleCount = $sorted->count();
        $people = $sorted->take(5);

        // Calculate summary stats (from all people, not just displayed)
        $stats = [
            'total_people' => $totalPeopleCount,
            'overdue_actions' => $directReports->sum('overdue_actions_count'),
            'due_soon_actions' => $directReports->sum('due_soon_actions_count'),
            'off_track_objectives' => $directReports->sum('off_track_objectives_count'),
        ];

        $hasMorePeople = $totalPeopleCount > 5;

        // All people including skip-levels for the quick-add/meeting dropdowns
        $allPeople = auth()->user()->allPeopleIncludingSkipLevels($company?->id);

        return view('dashboard', compact('people', 'stats', 'hasMorePeople', 'totalPeopleCount', 'allPeople'));
    }
}
