<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class InstallController extends Controller
{
    /**
     * Check if the application is already installed
     */
    public static function isInstalled(): bool
    {
        return File::exists(storage_path('installed'));
    }

    /**
     * Step 1: Check requirements
     */
    public function requirements()
    {
        if (self::isInstalled()) {
            return redirect('/');
        }

        $requirements = $this->checkRequirements();
        $allPassed = collect($requirements)->every(fn($r) => $r['passed']);

        return view('install.requirements', compact('requirements', 'allPassed'));
    }

    /**
     * Step 2: Database configuration
     */
    public function database()
    {
        if (self::isInstalled()) {
            return redirect('/');
        }

        $currentConfig = [
            'app_url' => env('APP_URL', 'https://'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', ''),
            'username' => env('DB_USERNAME', ''),
            'mail_from_address' => env('MAIL_FROM_ADDRESS', ''),
            'mail_from_name' => env('MAIL_FROM_NAME', 'MeetingMan'),
        ];

        return view('install.database', compact('currentConfig'));
    }

    /**
     * Step 2: Test and save database configuration
     */
    public function saveDatabase(Request $request)
    {
        if (self::isInstalled()) {
            return redirect('/');
        }

        $request->validate([
            'app_url' => 'required|url',
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        // Test the connection
        config([
            'database.connections.test_mysql' => [
                'driver' => 'mysql',
                'host' => $request->db_host,
                'port' => $request->db_port,
                'database' => $request->db_database,
                'username' => $request->db_username,
                'password' => $request->db_password ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
        ]);

        try {
            DB::connection('test_mysql')->getPdo();
        } catch (\Exception $e) {
            return back()->withErrors(['database' => 'Could not connect to database: ' . $e->getMessage()])->withInput();
        }

        // Generate APP_KEY if not set
        $appKey = env('APP_KEY');
        if (empty($appKey)) {
            $appKey = 'base64:' . base64_encode(random_bytes(32));
        }

        // Update .env file
        $this->updateEnvFile([
            'APP_URL' => rtrim($request->app_url, '/'),
            'APP_KEY' => $appKey,
            'DB_HOST' => $request->db_host,
            'DB_PORT' => $request->db_port,
            'DB_DATABASE' => $request->db_database,
            'DB_USERNAME' => $request->db_username,
            'DB_PASSWORD' => $request->db_password ?? '',
            'MAIL_MAILER' => 'sendmail',
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME' => $request->mail_from_name,
        ]);

        // Clear config cache to pick up new values
        Artisan::call('config:clear');

        return redirect()->route('install.migrate');
    }

    /**
     * Step 3: Run migrations
     */
    public function migrate()
    {
        if (self::isInstalled()) {
            return redirect('/');
        }

        return view('install.migrate');
    }

    /**
     * Step 3: Execute migrations
     */
    public function runMigrations()
    {
        if (self::isInstalled()) {
            return redirect('/');
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return redirect()->route('install.admin')->with('migration_output', $output);
        } catch (\Exception $e) {
            return back()->withErrors(['migration' => 'Migration failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Step 4: Create admin user
     */
    public function admin()
    {
        if (self::isInstalled()) {
            return redirect('/');
        }

        // Check if users table exists and has users
        try {
            $hasUsers = Schema::hasTable('users') && User::count() > 0;
        } catch (\Exception $e) {
            $hasUsers = false;
        }

        return view('install.admin', compact('hasUsers'));
    }

    /**
     * Step 4: Save admin user
     */
    public function saveAdmin(Request $request)
    {
        if (self::isInstalled()) {
            return redirect('/');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
            'is_super_admin' => true,
        ]);

        return redirect()->route('install.finalize');
    }

    /**
     * Step 5: Finalize installation
     */
    public function finalize()
    {
        if (self::isInstalled()) {
            return redirect('/');
        }

        return view('install.finalize');
    }

    /**
     * Step 5: Complete installation
     */
    public function complete()
    {
        if (self::isInstalled()) {
            return redirect('/');
        }

        try {
            // Create storage link if it doesn't exist
            if (!File::exists(public_path('storage'))) {
                Artisan::call('storage:link');
            }

            // Generate app key if not set
            if (empty(env('APP_KEY'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // Switch to database-backed sessions and cache now that DB is configured
            $this->updateEnvFile([
                'SESSION_DRIVER' => 'database',
                'CACHE_STORE' => 'database',
                'APP_DEBUG' => 'false',
            ]);

            // Cache configuration for production
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            // Mark as installed
            File::put(storage_path('installed'), now()->toIso8601String());

            return redirect()->route('install.success');
        } catch (\Exception $e) {
            return back()->withErrors(['finalize' => 'Finalization failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Installation complete
     */
    public function success()
    {
        if (!self::isInstalled()) {
            return redirect()->route('install.requirements');
        }

        return view('install.success');
    }

    /**
     * Check server requirements
     */
    private function checkRequirements(): array
    {
        return [
            [
                'name' => 'PHP Version',
                'required' => '8.2+',
                'current' => PHP_VERSION,
                'passed' => version_compare(PHP_VERSION, '8.2.0', '>='),
            ],
            [
                'name' => 'BCMath Extension',
                'required' => 'Enabled',
                'current' => extension_loaded('bcmath') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('bcmath'),
            ],
            [
                'name' => 'Ctype Extension',
                'required' => 'Enabled',
                'current' => extension_loaded('ctype') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('ctype'),
            ],
            [
                'name' => 'JSON Extension',
                'required' => 'Enabled',
                'current' => extension_loaded('json') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('json'),
            ],
            [
                'name' => 'Mbstring Extension',
                'required' => 'Enabled',
                'current' => extension_loaded('mbstring') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('mbstring'),
            ],
            [
                'name' => 'OpenSSL Extension',
                'required' => 'Enabled',
                'current' => extension_loaded('openssl') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('openssl'),
            ],
            [
                'name' => 'PDO Extension',
                'required' => 'Enabled',
                'current' => extension_loaded('pdo') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('pdo'),
            ],
            [
                'name' => 'PDO MySQL Extension',
                'required' => 'Enabled',
                'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('pdo_mysql'),
            ],
            [
                'name' => 'Tokenizer Extension',
                'required' => 'Enabled',
                'current' => extension_loaded('tokenizer') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('tokenizer'),
            ],
            [
                'name' => 'XML Extension',
                'required' => 'Enabled',
                'current' => extension_loaded('xml') ? 'Enabled' : 'Disabled',
                'passed' => extension_loaded('xml'),
            ],
            [
                'name' => 'Storage Directory Writable',
                'required' => 'Writable',
                'current' => is_writable(storage_path()) ? 'Writable' : 'Not Writable',
                'passed' => is_writable(storage_path()),
            ],
            [
                'name' => 'Bootstrap Cache Writable',
                'required' => 'Writable',
                'current' => is_writable(base_path('bootstrap/cache')) ? 'Writable' : 'Not Writable',
                'passed' => is_writable(base_path('bootstrap/cache')),
            ],
            [
                'name' => '.env File Writable',
                'required' => 'Writable',
                'current' => is_writable(base_path('.env')) ? 'Writable' : 'Not Writable',
                'passed' => is_writable(base_path('.env')),
            ],
        ];
    }

    /**
     * Update .env file with new values
     */
    private function updateEnvFile(array $values): void
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        foreach ($values as $key => $value) {
            // Escape special characters in the value
            $escapedValue = $value;
            if (preg_match('/\s|#|"/', $value)) {
                $escapedValue = '"' . addslashes($value) . '"';
            }

            // Check if key exists
            if (preg_match("/^{$key}=/m", $envContent)) {
                // Update existing key
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$escapedValue}",
                    $envContent
                );
            } else {
                // Add new key
                $envContent .= "\n{$key}={$escapedValue}";
            }
        }

        File::put($envPath, $envContent);
    }
}
