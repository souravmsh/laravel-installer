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
    protected $signature = 'laravel-installer:reset';

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

        // ── Step 1: Fix write permissions ─────────────────────────────────────
        $this->info('');
        $this->info('[ Step 1 ] Fixing file & directory permissions...');
        $this->fixPermissions();

        // ── Step 2: Resolve lock file path BEFORE clearing caches ────────────
        // Read config now so that any subsequent cache clears don't affect the path.
        $installLockFile = storage_path(
            config('laravel_installer.installed_key_path', 'app/private/key.install')
        );
        $licenseLockFile = storage_path(
            config('laravel_installer.license_storage_path', 'app/private/key.private')
        );

        $this->info('');
        $this->info('[ Step 2 ] Removing installation flags...');
        $this->line("  Install lock : <comment>{$installLockFile}</comment>");
        $this->line("  License lock : <comment>{$licenseLockFile}</comment>");

        foreach ([$installLockFile, $licenseLockFile] as $lockFile) {
            if (File::exists($lockFile)) {
                File::delete($lockFile);
                $this->info('  ✓ Removed: ' . basename($lockFile));
            }
        }

        // ── Step 3: Clear ALL caches (including bootstrap/cache/config.php) ───
        $this->info('');
        $this->info('[ Step 3 ] Clearing all application caches...');
        Artisan::call('optimize:clear');
        $this->info('  ✓ All caches cleared.');

        // ── Final check ───────────────────────────────────────────────────────
        if (!config('laravel_installer.installer_enabled', true)) {
            $this->warn('');
            $this->warn('⚠  INSTALLER_ENABLED is set to false in your .env file.');
            $this->warn('   Set INSTALLER_ENABLED=true in .env to re-enable setup mode.');
        }

        $this->info('');
        $this->info('✓ Installer reset complete. Visit your application to start the setup wizard.');

        return Command::SUCCESS;
    }

    /**
     * Fix write permissions on files and directories the installer needs to write.
     *
     * PHP's chmod() only works when the PHP process owns the target file.
     * When that fails, we fall back to a shell `chmod` so the fix actually lands
     * (e.g. files owned by root or a different deploy user).
     */
    protected function fixPermissions(): void
    {
        $envPath            = base_path('.env');
        $storagePath        = storage_path();
        $bootstrapCachePath = base_path('bootstrap/cache');
        $privateDir         = storage_path('app/private');

        $targets = [
            ['path' => $envPath,            'isDir' => false, 'mode' => 0664, 'label' => '.env'],
            ['path' => $storagePath,        'isDir' => true,  'mode' => 0775, 'label' => 'storage/'],
            ['path' => $bootstrapCachePath, 'isDir' => true,  'mode' => 0775, 'label' => 'bootstrap/cache/'],
            ['path' => $privateDir,         'isDir' => true,  'mode' => 0775, 'label' => 'storage/app/private/'],
        ];

        foreach ($targets as $target) {
            $path = $target['path'];

            // Ensure directories exist before chmodding
            if ($target['isDir'] && !File::exists($path)) {
                File::makeDirectory($path, 0775, true, true);
                $this->line("  ✓ Created  : {$target['label']}");
                continue;
            }

            if (!file_exists($path)) {
                $this->warn("  ⚠ Missing  : {$target['label']} (skipped)");
                continue;
            }

            $modeStr = decoct($target['mode']);

            if ($target['isDir']) {
                // chmod the directory itself and all its contents recursively
                $result = $this->chmodRecursive($path, $target['mode']);

                // Fallback: shell chmod -R for when PHP process doesn't own the files
                if (!$result) {
                    $escapedPath = escapeshellarg($path);
                    exec("chmod -R {$modeStr} {$escapedPath} 2>&1", $output, $exitCode);
                    $result = ($exitCode === 0);
                }
            } else {
                $result = @chmod($path, $target['mode']);

                // Fallback: shell chmod for single file
                if (!$result) {
                    $escapedPath = escapeshellarg($path);
                    exec("chmod {$modeStr} {$escapedPath} 2>&1", $output, $exitCode);
                    $result = ($exitCode === 0);
                }
            }

            if ($result) {
                $this->info("  ✓ chmod {$modeStr} : {$target['label']}");
            } else {
                $this->warn("  ⚠ Failed   : {$target['label']} (permission denied — try: sudo chmod -R {$modeStr} {$path})");
            }
        }
    }

    /**
     * Recursively chmod a directory and all its contents.
     */
    protected function chmodRecursive(string $path, int $mode): bool
    {
        $success = @chmod($path, $mode);

        if (is_dir($path)) {
            foreach (new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            ) as $item) {
                $itemMode = $item->isDir() ? $mode : ($mode & 0664); // files: strip execute bit
                @chmod($item->getPathname(), $itemMode);
            }
        }

        return $success;
    }

}
