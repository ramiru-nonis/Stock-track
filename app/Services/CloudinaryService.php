<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

class CloudinaryService
{
    protected ?Cloudinary $cloudinary = null;

    public function __construct()
    {
        $cloudName = (string) config('services.cloudinary.cloud_name', '');
        $apiKey = (string) config('services.cloudinary.api_key', '');
        $apiSecret = (string) config('services.cloudinary.api_secret', '');

        if (!empty($cloudName) && !empty($apiKey) && !empty($apiSecret)) {
            $this->cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key'    => $apiKey,
                    'api_secret' => $apiSecret,
                ],
            ]);
        }
    }

    public function isConfigured(): bool
    {
        return $this->cloudinary !== null;
    }

    /**
     * Upload an image to Cloudinary CDN.
     */
    public function upload(string|UploadedFile $file, string $folder = 'products'): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

            $result = $this->cloudinary->uploadApi()->upload($filePath, [
                'folder' => $folder,
                'resource_type' => 'image',
            ]);

            return [
                'public_id' => $result['public_id'] ?? null,
                'secure_url' => $result['secure_url'] ?? null,
                'url' => $result['url'] ?? null,
            ];
        } catch (Throwable $e) {
            Log::error('Cloudinary API upload error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete an asset from Cloudinary by public ID.
     */
    public function destroy(string $publicId): bool
    {
        if (!$this->isConfigured() || empty($publicId)) {
            return false;
        }

        try {
            $this->cloudinary->uploadApi()->destroy($publicId);
            return true;
        } catch (Throwable $e) {
            Log::error('Cloudinary API delete error: ' . $e->getMessage());
            return false;
        }
    }
}
