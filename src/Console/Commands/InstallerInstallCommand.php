<?php

namespace Souravmsh\Installer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallerInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-installer:install {--force : Overwrite any existing configuration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Laravel Installer package and publish its configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->components->info('Installing Laravel Installer...');

        $this->components->task('Publishing configuration', function () {
            $params = ['--tag' => 'laravel-installer-config'];
            if ($this->option('force')) {
                $params['--force'] = true;
            }
            Artisan::call('vendor:publish', $params);
            return true;
        });

        $this->components->info('Laravel Installer installed successfully.');
        $this->line('You can now configure the package in <comment>config/laravel_installer.php</comment>.');
        
        return self::SUCCESS;
    }
}
