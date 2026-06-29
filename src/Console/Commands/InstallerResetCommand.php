<?php

namespace Souravmsh\Installer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallerResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'laravel-installer:reset
                            {--force : Skip all prompts and remove everything including the license key}';

    /**
     * The console command description.
     */
    protected $description = 'Reset the installer. Removes install lock, caches, and permissions. Asks before removing license key (use --force to skip).';

    /**
     * Extended help text — shown when the command is run with --help.
     */
    protected $help = <<<'HELP'
<info>Usage:</info>
  laravel-installer:reset [options]

<info>Options:</info>
  <comment>(no flag)</comment>   Removes everything <question>except</question> the license key is asked first.
               Steps: fix permissions → remove install lock → <question>confirm</question> license key removal → clear caches.

  <comment>--force</comment>     Removes everything without any prompts.
               Steps: fix permissions → remove install lock → remove license key → clear caches.

<info>Examples:</info>
  <comment>php artisan laravel-installer:reset</comment>
      Resets the app to setup mode. Prompts you before removing the license key.

  <comment>php artisan laravel-installer:reset --force</comment>
      Wipes everything (install lock + license key + caches) immediately. No questions asked.

<info>Files removed:</info>
  Install lock  →  storage/app/private/key.install  (controls setup mode)
  License key   →  storage/app/private/key.private  (asked on normal reset, auto-removed on --force)
HELP;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $installLockFile = storage_path(
            config('laravel_installer.installed_key_path', 'app/private/key.install')
        );
        $licenseLockFile = storage_path(
            config('laravel_installer.license_storage_path', 'app/private/key.private')
        );

        // ── Step 1: Fix permissions ───────────────────────────────────────────
        $this->info('[ Step 1 ] Fixing file & directory permissions...');
        $this->fixPermissions();

        // ── Step 2: Remove install lock (always) ─────────────────────────────
        $this->newLine();
        $this->info('[ Step 2 ] Removing installation lock...');
        if (File::exists($installLockFile)) {
            File::delete($installLockFile);
            $this->info('  ✓ Removed: ' . basename($installLockFile));
        } else {
            $this->warn('  ⚠ Not found: ' . basename($installLockFile));
        }

        // ── Step 3: License key — prompt unless --force ───────────────────────
        $this->newLine();
        $this->info('[ Step 3 ] License key...');

        $removeLicense = $this->option('force')
            || $this->confirm("  Remove the stored license key? [{$licenseLockFile}]", false);

        if ($removeLicense) {
            if (File::exists($licenseLockFile)) {
                File::delete($licenseLockFile);
                $this->info('  ✓ Removed: ' . basename($licenseLockFile));
            } else {
                $this->warn('  ⚠ Not found: ' . basename($licenseLockFile));
            }
        } else {
            $this->line('  – License key kept.');
        }

        // ── Step 4: Clear all caches ─────────────────────────────────────────
        $this->newLine();
        $this->info('[ Step 4 ] Clearing all application caches...');
        Artisan::call('optimize:clear');
        $this->info('  ✓ All caches cleared.');

        if (!config('laravel_installer.installer_enabled', true)) {
            $this->newLine();
            $this->warn('⚠  INSTALLER_ENABLED is false — set INSTALLER_ENABLED=true in .env to re-enable setup mode.');
        }

        $this->newLine();
        $this->info('✓ Reset complete. Visit your application to start the setup wizard.');

        return self::SUCCESS;
    }

    /**
     * Fix write permissions on directories the installer needs.
     */
    protected function fixPermissions(): void
    {
        $targets = [
            ['path' => base_path('.env'),            'isDir' => false, 'mode' => 0664, 'label' => '.env'],
            ['path' => storage_path(),               'isDir' => true,  'mode' => 0775, 'label' => 'storage/'],
            ['path' => base_path('bootstrap/cache'), 'isDir' => true,  'mode' => 0775, 'label' => 'bootstrap/cache/'],
            ['path' => storage_path('app/private'),  'isDir' => true,  'mode' => 0775, 'label' => 'storage/app/private/'],
        ];

        foreach ($targets as $target) {
            $path    = $target['path'];
            $modeStr = decoct($target['mode']);

            if ($target['isDir'] && !File::isDirectory($path)) {
                File::makeDirectory($path, 0775, true, true);
                $this->line("  ✓ Created  : {$target['label']}");
                continue;
            }

            if (!file_exists($path)) {
                $this->warn("  ⚠ Missing  : {$target['label']} (skipped)");
                continue;
            }

            $result = $target['isDir']
                ? $this->chmodRecursive($path, $target['mode'])
                : @chmod($path, $target['mode']);

            if (!$result) {
                exec('chmod ' . ($target['isDir'] ? '-R ' : '') . "{$modeStr} " . escapeshellarg($path) . ' 2>&1', $out, $code);
                $result = ($code === 0);
            }

            $this->line($result
                ? "  ✓ chmod {$modeStr} : {$target['label']}"
                : "  ⚠ Failed   : {$target['label']} (try: sudo chmod -R {$modeStr} {$path})"
            );
        }
    }

    /**
     * Recursively chmod a directory and all its contents.
     */
    protected function chmodRecursive(string $path, int $mode): bool
    {
        $success = @chmod($path, $mode);

        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        ) as $item) {
            @chmod($item->getPathname(), $item->isDir() ? $mode : ($mode & 0664));
        }

        return $success;
    }
}
