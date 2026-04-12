<?php

namespace Souravmsh\Installer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Souravmsh\Installer\Services\DatabaseService;
use Souravmsh\Installer\Services\LicenseService;

class InstallerController extends Controller
{
    protected $databaseService;
    protected $licenseService;

    public function __construct(DatabaseService $databaseService, LicenseService $licenseService)
    {
        $this->databaseService = $databaseService;
        $this->licenseService = $licenseService;
    }

    /**
     * Show welcome screen
     */
    public function welcome()
    {
        $requirements = $this->checkRequirements();
        return view('installer::welcome', compact('requirements'));
    }

    /**
     * Show database configuration form
     */
    public function database()
    {
        return view('installer::database');
    }

    /**
     * Test database connection
     */
    public function testDatabase(Request $request)
    {
        $request->validate([
            'host' => 'required',
            'port' => 'required|numeric',
            'database' => 'required',
            'username' => 'required',
            'password' => 'nullable',
        ]);

        $config = $request->only(['host', 'port', 'database', 'username', 'password']);

        $connectionResult = $this->databaseService->testConnection($config);

        if ($connectionResult === true) {
            return response()->json([
                'success' => true,
                'message' => 'Database connection successful!'
            ]);
        }

        if ($connectionResult === 'missing_database') {
            return response()->json([
                'success' => false,
                'missing_database' => true,
                'message' => "Database '{$config['database']}' does not exist."
            ], 422);
        }

        return response()->json([
            'success' => false,
            'message' => 'Database connection failed. Please check your credentials.'
        ], 422);
    }

    /**
     * Create database
     */
    public function createDatabase(Request $request)
    {
        $request->validate([
            'host' => 'required',
            'port' => 'required|numeric',
            'database' => 'required',
            'username' => 'required',
            'password' => 'nullable',
        ]);

        $config = $request->only(['host', 'port', 'database', 'username', 'password']);

        if ($this->databaseService->createDatabase($config)) {
            return response()->json([
                'success' => true,
                'message' => 'Database created successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to create database. Please check your user permissions.'
        ], 500);
    }

    /**
     * Save database configuration
     */
    public function saveDatabase(Request $request)
    {
        $request->validate([
            'host' => 'required',
            'port' => 'required|numeric',
            'database' => 'required',
            'username' => 'required',
            'password' => 'nullable',
        ]);

        $config = $request->only(['host', 'port', 'database', 'username', 'password']);

        // Test connection first
        if (!$this->databaseService->testConnection($config)) {
            return back()->withErrors(['database' => 'Database connection failed.']);
        }

        // Update .env file
        if (!$this->databaseService->updateEnvFile($config)) {
            return back()->withErrors(['database' => 'Failed to update configuration file.']);
        }

        // Update runtime config and reconnect to the new database
        config([
            'database.connections.mysql.host' => $config['host'],
            'database.connections.mysql.port' => $config['port'],
            'database.connections.mysql.database' => $config['database'],
            'database.connections.mysql.username' => $config['username'],
            'database.connections.mysql.password' => $config['password'],
        ]);
        \Illuminate\Support\Facades\DB::purge('mysql');
        \Illuminate\Support\Facades\DB::reconnect('mysql');

        // Run migrations
        $migrationResult = $this->databaseService->runMigrations();
        if ($migrationResult !== true) {
            return back()->withErrors(['database' => is_string($migrationResult) ? $migrationResult : 'Failed to run migrations.']);
        }

        // Run seeders
        $seederResult = $this->databaseService->runSeeders();
        if ($seederResult !== true) {
            return back()->withErrors(['database' => is_string($seederResult) ? $seederResult : 'Failed to run seeders.']);
        }

        // Check if license check is enabled
        if (config('laravel_installer.license_check', 'required') === 'disabled') {
            return redirect()->route('installer.install')->with('success', 'Database configured successfully!');
        }

        return redirect()->route('installer.license')->with('success', 'Database configured successfully!');
    }

    /**
     * Show license form
     */
    public function license()
    {
        // Check if license check is enabled
        if (config('laravel_installer.license_check', 'required') === 'disabled') {
            return redirect()->route('installer.install');
        }

        return view('installer::license');
    }

    /**
     * Validate and save license
     */
    public function saveLicense(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'license_key' => 'required|string',
        ]);

        $validation = $this->licenseService->validate(
            $request->name,
            $request->email,
            $request->license_key
        );

        if (!$validation['success']) {
            return back()->withErrors(['license' => $validation['message']]);
        }

        // Store in session for now (will save to DB after migrations)
        session([
            'license_name' => $request->name,
            'license_email' => $request->email,
            'license_key' => $request->license_key,
            'license_data' => $validation['data'] ?? []
        ]);

        return redirect()->route('installer.install')->with('success', 'License validated successfully!');
    }

    /**
     * Run installation (migrations, seeders, etc.)
     */
    public function install()
    {
        return view('installer::install');
    }

    /**
     * Process installation
     */
    public function processInstall()
    {
        try {
            // Store license information if enabled
            if (config('laravel_installer.license_check', 'required') !== 'disabled' && session()->has('license_key')) {
                $this->licenseService->storeLicense(
                    session('license_name'),
                    session('license_email'),
                    session('license_key'),
                    session('license_data', [])
                );
            }

            // Create install lock file
            File::put(storage_path(config('laravel_installer.installed_key_path', 'app/private/key.install')), now()->toDateTimeString());

            // Clear session
            session()->forget(['license_name', 'license_email', 'license_key', 'license_data']);

            return response()->json([
                'success' => true,
                'message' => 'Installation completed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Installation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show completion screen
     */
    public function complete()
    {
        return view('installer::complete');
    }

    protected function checkRequirements(): array
    {
        // Try to create .env from .env.example if it doesn't exist
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');
        
        if (!File::exists($envPath) && File::exists($envExamplePath)) {
            try {
                File::copy($envExamplePath, $envPath);
            } catch (\Exception $e) {
                // Ignore, let the status check handle the failure
            }
        }

        return [
            'php_version' => [
                'name' => 'PHP Version >= 8.1',
                'status' => version_compare(PHP_VERSION, '8.1.0', '>='),
                'current' => PHP_VERSION
            ],
            'pdo' => [
                'name' => 'PDO Extension',
                'status' => extension_loaded('pdo'),
            ],
            'mbstring' => [
                'name' => 'Mbstring Extension',
                'status' => extension_loaded('mbstring'),
            ],
            'openssl' => [
                'name' => 'OpenSSL Extension',
                'status' => extension_loaded('openssl'),
            ],
            'tokenizer' => [
                'name' => 'Tokenizer Extension',
                'status' => extension_loaded('tokenizer'),
            ],
            'xml' => [
                'name' => 'XML Extension',
                'status' => extension_loaded('xml'),
            ],
            'ctype' => [
                'name' => 'Ctype Extension',
                'status' => extension_loaded('ctype'),
            ],
            'json' => [
                'name' => 'JSON Extension',
                'status' => extension_loaded('json'),
            ],
            'storage_writable' => [
                'name' => 'Storage Directory Writable',
                'status' => is_writable(storage_path()),
            ],
            'env_writable' => [
                'name' => '.env File Writable',
                'status' => file_exists(app()->environmentFilePath()) 
                            ? is_writable(app()->environmentFilePath()) 
                            : is_writable(dirname(app()->environmentFilePath())),
            ],
        ];
    }
}
