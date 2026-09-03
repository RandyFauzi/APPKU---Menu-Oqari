<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaService
{
    protected ImageManager $imageManager;
    protected string $disk;

    public function __construct()
    {
        // Using GD Driver by default
        $this->imageManager = new ImageManager(new Driver());
        
        // In the future, this can be swapped to 's3' or 'r2' via config
        $this->disk = config('filesystems.default', 'public');
    }

    /**
     * Resolve the public URL for a given media path.
     * This decouples the domain models from knowing the storage implementation.
     */
    public function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // If it's already an absolute URL, return it directly
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Process and store a product image.
     * Validates, strips EXIF, resizes to max 800x800, and converts to WebP.
     */
    public function storeProductImage(UploadedFile $file, string $shopId, string $productId): string
    {
        $directory = "media/shops/{$shopId}/products/{$productId}";
        $filename = 'image_' . Str::random(10) . '.webp';
        
        return $this->processAndStore($file, $directory, $filename, 800, 800);
    }

    /**
     * Process and store a shop branding image (logo, banners).
     */
    public function storeShopBranding(UploadedFile $file, string $shopId, string $prefix): string
    {
        $directory = "media/shops/{$shopId}/branding";
        $filename = "{$prefix}_" . Str::random(10) . '.webp';
        
        // Banners might need different dimensions, but for now we cap at 1200 max dimension
        $maxSize = $prefix === 'logo' ? 800 : 1200;
        
        return $this->processAndStore($file, $directory, $filename, $maxSize, $maxSize);
    }

    /**
     * Core image processing logic using Intervention Image v3.
     */
    protected function processAndStore(UploadedFile $file, string $directory, string $filename, int $maxWidth, int $maxHeight): string
    {
        // Ensure the directory exists if using local disk
        if ($this->disk === 'public') {
            if (!Storage::disk($this->disk)->exists($directory)) {
                Storage::disk($this->disk)->makeDirectory($directory);
            }
        }

        $image = $this->imageManager->read($file->getRealPath());

        // Scale down to max dimensions, keeping aspect ratio
        $image->scaleDown(width: $maxWidth, height: $maxHeight);

        // Encode to WebP with 80% quality (implicitly strips EXIF)
        $encodedImage = $image->toWebp(80);

        $path = "{$directory}/{$filename}";

        // Store the optimized file content
        Storage::disk($this->disk)->put($path, $encodedImage->toString());

        return $path;
    }
}
