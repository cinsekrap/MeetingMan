<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Person;
use App\Models\User;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    public function __construct(
        protected AdminAuditService $auditService
    ) {}

    public function index(Request $request)
    {
        $query = User::query()
            ->withCount(['people', 'meetings']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->input('filter') === 'suspended') {
            $query->whereNotNull('suspended_at');
        } elseif ($request->input('filter') === 'admins') {
            $query->where('is_super_admin', true);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->loadCount(['people', 'meetings']);

        return view('admin.users.show', compact('user'));
    }

    public function suspend(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot suspend yourself.');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot suspend a super admin. Demote them first.');
        }

        $user->update(['suspended_at' => now()]);

        $this->auditService->log(
            admin: $request->user(),
            action: 'suspend_user',
            targetUser: $user,
            justification: $request->input('reason', 'No reason provided')
        );

        return back()->with('success', "{$user->name} has been suspended.");
    }

    public function unsuspend(Request $request, User $user)
    {
        $user->update(['suspended_at' => null]);

        $this->auditService->log(
            admin: $request->user(),
            action: 'unsuspend_user',
            targetUser: $user
        );

        return back()->with('success', "{$user->name} has been unsuspended.");
    }

    public function forcePasswordReset(Request $request, User $user)
    {
        $temporaryPassword = Str::random(16);
        $user->update(['password' => Hash::make($temporaryPassword)]);

        $this->auditService->log(
            admin: $request->user(),
            action: 'force_password_reset',
            targetUser: $user
        );

        return back()->with('success', "Password reset for {$user->name}. Temporary password: {$temporaryPassword}");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot delete a super admin. Demote them first.');
        }

        $userName = $user->name;

        $this->auditService->log(
            admin: $request->user(),
            action: 'delete_user',
            targetUser: $user,
            metadata: [
                'deleted_user_name' => $user->name,
                'deleted_user_email' => $user->email,
            ]
        );

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "{$userName} has been deleted.");
    }

    public function promote(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('warning', "{$user->name} is already a super admin.");
        }

        $user->update(['is_super_admin' => true]);

        $this->auditService->log(
            admin: $request->user(),
            action: 'promote_admin',
            targetUser: $user
        );

        return back()->with('success', "{$user->name} has been promoted to super admin.");
    }

    public function demote(Request $request, User $user)
    {
        if (! $user->isSuperAdmin()) {
            return back()->with('warning', "{$user->name} is not a super admin.");
        }

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot demote yourself.');
        }

        $remainingAdmins = User::where('is_super_admin', true)
            ->where('id', '!=', $user->id)
            ->count();

        if ($remainingAdmins === 0) {
            return back()->with('error', 'Cannot demote the last super admin.');
        }

        $user->update(['is_super_admin' => false]);

        $this->auditService->log(
            admin: $request->user(),
            action: 'demote_admin',
            targetUser: $user
        );

        return back()->with('success', "{$user->name} has been demoted from super admin.");
    }

    public function viewData(Request $request, User $user)
    {
        $request->validate([
            'justification' => 'required|string|min:10|max:500',
        ]);

        $this->auditService->log(
            admin: $request->user(),
            action: 'view_user_data',
            targetUser: $user,
            justification: $request->input('justification')
        );

        $people = $user->people()->withCount('meetings')->get();
        $recentMeetings = Meeting::whereIn('person_id', $people->pluck('id'))
            ->with(['person', 'topics', 'actions'])
            ->orderBy('meeting_date', 'desc')
            ->take(20)
            ->get();

        return view('admin.users.data', compact('user', 'people', 'recentMeetings'));
    }
}
