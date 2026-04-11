<?php

namespace Souravmsh\Installer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class LicenseService
{
    /**
     * License server endpoint (configurable via .env)
     */
    protected function getLicenseServerUrl(): string
    {
        return config('laravel_installer.license_server_url', 'https://codekernel.net/api/v1/license');
    }

    /**
     * Validate license with central server
     */
    public function validate(string $name, string $email, string $licenseKey): array
    {
        try {
            $response = Http::timeout(10)->post($this->getLicenseServerUrl(), [
                'name' => $name,
                'email' => $email,
                'license_key' => $licenseKey,
                'domain' => request()->getHost(),
                'app_url' => config('app.url'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['valid']) && $data['valid'] === true) {
                    return [
                        'success' => true,
                        'message' => $data['message'] ?? 'License validated successfully',
                        'data' => $data
                    ];
                }
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Invalid license key',
            ];
        } catch (\Exception $e) {
            // If license server is unreachable, allow installation (optional)
            // You can change this behavior based on your requirements
            return [
                'success' => true,
                'message' => 'License server unreachable. Installation allowed.',
                'warning' => true
            ];
        }
    }

    /**
     * Store license information in database
     */
    public function storeLicense(string $name, string $email, string $licenseKey, array $validationData = []): bool
    {
        try {
            DB::table('settings')->updateOrInsert(
                ['key' => 'license_name'],
                ['value' => $name, 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'license_email'],
                ['value' => $email, 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'license_key'],
                ['value' => $licenseKey, 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'license_validated_at'],
                ['value' => now()->toDateTimeString(), 'updated_at' => now()]
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
