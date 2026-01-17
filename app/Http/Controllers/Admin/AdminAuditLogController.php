<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminAuditLog::with(['admin', 'targetUser'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->input('admin_id'));
        }

        if ($request->filled('target_user_id')) {
            $query->where('target_user_id', $request->input('target_user_id'));
        }

        $logs = $query->paginate(50);

        $actions = AdminAuditLog::select('action')
            ->distinct()
            ->pluck('action');

        return view('admin.audit-logs.index', compact('logs', 'actions'));
    }

    public function show(AdminAuditLog $auditLog)
    {
        $auditLog->load(['admin', 'targetUser']);

        return view('admin.audit-logs.show', compact('auditLog'));
    }
}
