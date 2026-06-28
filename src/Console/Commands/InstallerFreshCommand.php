<?php

namespace Souravmsh\Installer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallerFreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-installer:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear application cache and remove the installation flag to refresh the setup process.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('WARNING: You are about to remove the installation flag and clear all application caches.');
        $this->warn('This will force the application back into setup mode.');
        
        if (!$this->confirm('Are you sure you want to refresh the installer setup?')) {
            $this->info('Setup refresh cancelled.');
            return Command::SUCCESS;
        }

        $this->info('Clearing application caches...');
        
        // Clear caches
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->info('Caches cleared successfully.');

        // Remove installed flag
        $installLockFile = storage_path(config('laravel_installer.installed_key_path', 'app/private/key.install'));
        
        if (File::exists($installLockFile)) {
            File::delete($installLockFile);
            $this->info('Installation flag removed successfully.');
        } else {
            $this->info('Installation flag not found. The app may already be in setup mode.');
        }

        $this->info('The installer has been refreshed. You can now revisit the setup wizard.');
        
        return Command::SUCCESS;
    }
}
