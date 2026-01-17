<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAuditService;
use App\Services\UpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUpdateController extends Controller
{
    public function __construct(
        protected UpdateService $updateService,
        protected AdminAuditService $auditService
    ) {}

    public function index(): View
    {
        $currentVersion = $this->updateService->getCurrentVersion();

        return view('admin.updates.index', [
            'currentVersion' => $currentVersion,
        ]);
    }

    public function check(): JsonResponse
    {
        $currentVersion = $this->updateService->getCurrentVersion();
        $latestRelease = $this->updateService->getLatestVersion();

        if (!$latestRelease) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to check for updates. Please try again later.',
            ], 500);
        }

        $updateAvailable = version_compare($latestRelease['version'], $currentVersion, '>');

        return response()->json([
            'success' => true,
            'current_version' => $currentVersion,
            'latest_version' => $latestRelease['version'],
            'update_available' => $updateAvailable,
            'release_name' => $latestRelease['name'],
            'changelog' => $latestRelease['body'],
            'published_at' => $latestRelease['published_at'],
            'release_url' => $latestRelease['html_url'],
        ]);
    }

    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'version' => 'required|string',
        ]);

        $version = $request->input('version');
        $currentVersion = $this->updateService->getCurrentVersion();

        // Verify update is actually newer
        if (!version_compare($version, $currentVersion, '>')) {
            return response()->json([
                'success' => false,
                'message' => 'The specified version is not newer than the current version.',
            ], 400);
        }

        // Log the update attempt
        $this->auditService->log(
            admin: auth()->user(),
            action: 'update_initiated',
            metadata: [
                'from_version' => $currentVersion,
                'to_version' => $version,
            ]
        );

        // Download the update
        $zipPath = $this->updateService->downloadUpdate($version);

        if (!$zipPath) {
            $this->auditService->log(
                admin: auth()->user(),
                action: 'update_download_failed',
                metadata: [
                    'version' => $version,
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Failed to download the update. Please check the logs for details.',
            ], 500);
        }

        // Apply the update
        $success = $this->updateService->applyUpdate($zipPath, $version);

        if ($success) {
            $this->auditService->log(
                admin: auth()->user(),
                action: 'update_completed',
                metadata: [
                    'from_version' => $currentVersion,
                    'to_version' => $version,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "Successfully updated to version {$version}. Please refresh the page.",
                'new_version' => $version,
            ]);
        }

        $this->auditService->log(
            admin: auth()->user(),
            action: 'update_apply_failed',
            metadata: [
                'version' => $version,
            ]
        );

        return response()->json([
            'success' => false,
            'message' => 'Failed to apply the update. Please check the logs for details.',
        ], 500);
    }
}
