<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Company;
use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereNull('suspended_at')->count(),
            'suspended_users' => User::whereNotNull('suspended_at')->count(),
            'super_admins' => User::where('is_super_admin', true)->count(),
            'new_users_this_week' => User::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'new_users_this_month' => User::where('created_at', '>=', Carbon::now()->subMonth())->count(),
            'total_meetings' => Meeting::count(),
            'meetings_this_week' => Meeting::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'meetings_this_month' => Meeting::where('created_at', '>=', Carbon::now()->subMonth())->count(),
            'total_companies' => Company::count(),
            'new_companies_this_week' => Company::where('created_at', '>=', Carbon::now()->subWeek())->count(),
        ];

        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $recentCompanies = Company::withCount('users', 'people')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentActivity = AdminAuditLog::with(['admin', 'targetUser'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentCompanies', 'recentActivity'));
    }
}
