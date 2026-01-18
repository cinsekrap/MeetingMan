<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UpdateService
{
    protected string $githubRepo = 'cinsekrap/MeetingMan';
    protected array $protectedPaths = [
        '.env',
        'storage/installed',
        'storage/logs',
        'storage/app',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'bootstrap/cache',
        'public/branding',
    ];

    public function getCurrentVersion(): string
    {
        $versionFile = base_path('VERSION');

        if (File::exists($versionFile)) {
            return trim(File::get($versionFile));
        }

        return '0.0.0';
    }

    public function getLatestVersion(): ?array
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'MeetingMan-Updater',
            ])->get("https://api.github.com/repos/{$this->githubRepo}/releases/latest");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'version' => ltrim($data['tag_name'] ?? '', 'v'),
                    'tag' => $data['tag_name'] ?? '',
                    'name' => $data['name'] ?? '',
                    'body' => $data['body'] ?? '',
                    'published_at' => $data['published_at'] ?? null,
                    'html_url' => $data['html_url'] ?? '',
                    'zipball_url' => $data['zipball_url'] ?? '',
                ];
            }

            Log::warning('Failed to fetch latest version from GitHub', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error checking for updates', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function isUpdateAvailable(): bool
    {
        $current = $this->getCurrentVersion();
        $latest = $this->getLatestVersion();

        if (!$latest) {
            return false;
        }

        return version_compare($latest['version'], $current, '>');
    }

    public function downloadUpdate(string $version): ?string
    {
        try {
            $tag = 'v' . ltrim($version, 'v');
            $downloadUrl = "https://github.com/{$this->githubRepo}/archive/refs/tags/{$tag}.zip";

            $tempPath = storage_path('app/updates');

            if (!File::isDirectory($tempPath)) {
                File::makeDirectory($tempPath, 0755, true);
            }

            $zipPath = $tempPath . "/update-{$version}.zip";

            $response = Http::withOptions([
                'sink' => $zipPath,
                'timeout' => 120,
            ])->withHeaders([
                'User-Agent' => 'MeetingMan-Updater',
            ])->get($downloadUrl);

            if ($response->successful() && File::exists($zipPath) && File::size($zipPath) > 0) {
                Log::info('Update downloaded successfully', [
                    'version' => $version,
                    'path' => $zipPath,
                ]);

                return $zipPath;
            }

            Log::error('Failed to download update', [
                'version' => $version,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error downloading update', [
                'version' => $version,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function applyUpdate(string $zipPath, string $version): bool
    {
        try {
            $zip = new ZipArchive();

            if ($zip->open($zipPath) !== true) {
                Log::error('Failed to open update zip', ['path' => $zipPath]);
                return false;
            }

            $extractPath = storage_path('app/updates/extracted');

            if (File::isDirectory($extractPath)) {
                File::deleteDirectory($extractPath);
            }

            File::makeDirectory($extractPath, 0755, true);

            $zip->extractTo($extractPath);
            $zip->close();

            // Find the extracted directory (GitHub adds repo-version prefix)
            $directories = File::directories($extractPath);

            if (empty($directories)) {
                Log::error('No directory found in extracted update');
                return false;
            }

            $sourceDir = $directories[0];

            // Copy files while preserving protected paths
            $this->copyUpdateFiles($sourceDir, base_path());

            // Update VERSION file
            File::put(base_path('VERSION'), ltrim($version, 'v') . "\n");

            // Clear caches
            $this->clearCaches();

            // Cleanup
            File::deleteDirectory($extractPath);
            File::delete($zipPath);

            Log::info('Update applied successfully', ['version' => $version]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error applying update', [
                'version' => $version,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    protected function copyUpdateFiles(string $source, string $destination): void
    {
        $files = File::allFiles($source);

        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();

            // Skip protected paths
            if ($this->isProtectedPath($relativePath)) {
                continue;
            }

            $destPath = $destination . '/' . $relativePath;
            $destDir = dirname($destPath);

            if (!File::isDirectory($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }

            File::copy($file->getPathname(), $destPath);
        }

        // Also copy directories that might be empty in the source but needed
        $directories = File::directories($source);

        foreach ($directories as $dir) {
            $relativePath = basename($dir);

            if (!$this->isProtectedPath($relativePath)) {
                $this->copyUpdateFiles($dir, $destination . '/' . $relativePath);
            }
        }
    }

    protected function isProtectedPath(string $path): bool
    {
        $normalizedPath = str_replace('\\', '/', $path);

        foreach ($this->protectedPaths as $protected) {
            if (str_starts_with($normalizedPath, $protected) || $normalizedPath === $protected) {
                return true;
            }
        }

        return false;
    }

    protected function clearCaches(): void
    {
        try {
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            Log::info('Caches cleared after update');
        } catch (\Exception $e) {
            Log::warning('Failed to clear some caches', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getChangelog(string $version): ?string
    {
        try {
            $tag = 'v' . ltrim($version, 'v');

            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'MeetingMan-Updater',
            ])->get("https://api.github.com/repos/{$this->githubRepo}/releases/tags/{$tag}");

            if ($response->successful()) {
                return $response->json()['body'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching changelog', [
                'version' => $version,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function cleanupOldUpdates(): void
    {
        $updatePath = storage_path('app/updates');

        if (File::isDirectory($updatePath)) {
            $files = File::files($updatePath);

            foreach ($files as $file) {
                // Delete files older than 24 hours
                if (time() - $file->getMTime() > 86400) {
                    File::delete($file->getPathname());
                }
            }
        }
    }
}
