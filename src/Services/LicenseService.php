<?php

namespace Souravmsh\Installer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

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

                if (isset($data['status']) && $data['status'] === true) {
                    return [
                        'success' => true,
                        'message' => $data['message'] ?? 'License validated successfully',
                        'data' => $data['data'] ?? []
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
            $privateKey = $validationData['private_key'] ?? '';

            if (empty($privateKey)) {
                return false;
            }

            $licensePath = config('laravel_installer.license_storage_path', 'app/private/key.private');
            $directory = storage_path(dirname($licensePath));
            $filePath = storage_path($licensePath);

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            File::put($filePath, $privateKey);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
