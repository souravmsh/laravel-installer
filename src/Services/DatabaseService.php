<?php

namespace Souravmsh\Installer\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DatabaseService
{
    /**
     * Test database connection with given credentials
     */
    /**
     * Test database connection with given credentials
     * Returns true if successful, or error code/message string if failed
     */
    public function testConnection(array $config): bool|string
    {
        try {
            $connection = new \PDO(
                "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}",
                $config['username'],
                $config['password']
            );
            return true;
        } catch (\PDOException $e) {
            // Check for "Unknown database" error (MySQL error 1049)
            if ($e->getCode() == 1049) {
                return 'missing_database';
            }
            return false;
        }
    }

    /**
     * Create database
     */
    public function createDatabase(array $config): bool
    {
        try {
            // Connect without database name
            $connection = new \PDO(
                "mysql:host={$config['host']};port={$config['port']}",
                $config['username'],
                $config['password']
            );
            
            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // Create database
            $sql = "CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            $connection->exec($sql);
            
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Update .env file with database configuration
     */
    public function updateEnvFile(array $config): bool
    {
        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            // Copy from .env.example if .env doesn't exist
            if (File::exists(base_path('.env.example'))) {
                File::copy(base_path('.env.example'), $envPath);
            } else {
                return false;
            }
        }

        $envContent = File::get($envPath);

        // Update database configuration
        $envContent = preg_replace('/DB_HOST=.*/', 'DB_HOST=' . $config['host'], $envContent);
        $envContent = preg_replace('/DB_PORT=.*/', 'DB_PORT=' . $config['port'], $envContent);
        $envContent = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=' . $config['database'], $envContent);
        $envContent = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=' . $config['username'], $envContent);
        $envContent = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=' . $config['password'], $envContent);

        File::put($envPath, $envContent);

        // Clear config cache
        Artisan::call('config:clear');

        return true;
    }

    /**
     * Run migrations
     */
    public function runMigrations(): bool|string
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            
            // Check if there were any errors
            $output = Artisan::output();
            if (str_contains($output, 'error') || str_contains($output, 'failed')) {
                return $output;
            }
            
            return true;
        } catch (\Exception $e) {
            return 'Migration failed: ' . $e->getMessage();
        }
    }

    /**
     * Run seeders
     */
    public function runSeeders(): bool|string
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            
            // Check if there were any errors
            $output = Artisan::output();
            if (str_contains($output, 'error') || str_contains($output, 'failed')) {
                return $output;
            }
            
            return true;
        } catch (\Exception $e) {
            return 'Seeding failed: ' . $e->getMessage();
        }
    }
}
