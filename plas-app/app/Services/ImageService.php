<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Responsive image size configurations
     */
    protected $sizes = [
        'small' => ['width' => 400, 'height' => null],
        'medium' => ['width' => 800, 'height' => null],
        'large' => ['width' => 1200, 'height' => null],
    ];

    /**
     * Compression quality settings
     */
    protected $compressionLevels = [
        'small' => 70,    // Higher compression for small images
        'medium' => 75,   // Medium compression for medium images
        'large' => 80,    // Lower compression for large images
        'original' => 85  // Minimal compression for original images
    ];

    /**
     * Process and store an uploaded image with responsive versions
     *
     * @param UploadedFile $image The uploaded image file
     * @param string $path The storage path (e.g., 'news', 'providers')
     * @param bool $generateResponsive Whether to generate responsive versions
     * @param bool $maintainAspectRatio Whether to maintain aspect ratio when resizing
     * @return array The paths to the stored images
     */
    public function storeResponsive(
        UploadedFile $image, 
        string $path, 
        bool $generateResponsive = true,
        bool $maintainAspectRatio = true
    ): array {
        // Generate a unique filename with the original extension
        $extension = $image->getClientOriginalExtension();
        $baseFilename = Str::uuid();
        $result = [];
        
        // Process and store each size if responsive is enabled
        if ($generateResponsive) {
            foreach ($this->sizes as $sizeName => $dimensions) {
                $filename = "{$baseFilename}_{$sizeName}.{$extension}";
                $fullPath = $path . '/' . $filename;
                
                // Create image instance and resize
                $img = Image::make($image);
                
                // Resize based on dimensions
                if ($maintainAspectRatio) {
                    $img->resize($dimensions['width'], $dimensions['height'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } else {
                    $img->resize($dimensions['width'], $dimensions['height']);
                }
                
                // Apply advanced optimization
                $this->optimizeImage($img, $extension, $sizeName);
                
                // Store the processed image
                Storage::disk('public')->put($fullPath, $img->stream());
                
                $result["{$sizeName}"] = $fullPath;
            }
        }
        
        // Always store the original version
        $originalFilename = "{$baseFilename}_original.{$extension}";
        $originalFullPath = $path . '/' . $originalFilename;
        
        // Store the original image with minimal compression
        $originalImg = Image::make($image);
        $this->optimizeImage($originalImg, $extension, 'original');
        Storage::disk('public')->put($originalFullPath, $originalImg->stream());
        
        $result['original'] = $originalFullPath;
        
        return $result;
    }

    /**
     * Optimize an image based on its type and target size
     *
     * @param \Intervention\Image\Image $img The image instance
     * @param string $extension The image extension/format
     * @param string $sizeName The target size name (small, medium, large, original)
     * @return void
     */
    protected function optimizeImage($img, string $extension, string $sizeName): void
    {
        // Get the appropriate compression level for this size
        $quality = $this->compressionLevels[$sizeName] ?? 80;
        
        // Apply format-specific optimizations
        if (in_array(strtolower($extension), ['jpg', 'jpeg'])) {
            // For JPEG, we can apply some additional filtering
            $img->encode('jpg', $quality);
        } elseif (strtolower($extension) === 'png') {
            // For PNG, optimize with lower color depth if it's not the original
            if ($sizeName !== 'original') {
                // Try to reduce color palette for smaller file size
                $img->limitColors(256);
            }
            $img->encode('png', $quality);
        } else {
            // Default encoding for other formats
            $img->encode($extension, $quality);
        }
    }

    /**
     * Process and store an uploaded image with optimization (legacy method)
     *
     * @param UploadedFile $image The uploaded image file
     * @param string $path The storage path (e.g., 'news', 'providers')
     * @param int $width The width to resize to (null for no resizing)
     * @param int $height The height to resize to (null for no resizing)
     * @param bool $maintainAspectRatio Whether to maintain aspect ratio when resizing
     * @return string|null The path to the stored image or null if no image
     */
    public function store(
        UploadedFile $image, 
        string $path, 
        int $width = 1200, 
        int $height = null, 
        bool $maintainAspectRatio = true
    ): ?string {
        // Generate a unique filename with the original extension
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $fullPath = $path . '/' . $filename;
        
        // Create image instance and resize if dimensions provided
        $img = Image::make($image);
        
        if ($width || $height) {
            if ($maintainAspectRatio) {
                $img->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } else {
                $img->resize($width, $height);
            }
        }
        
        // Apply advanced optimization
        $this->optimizeImage($img, $image->getClientOriginalExtension(), 'large');
        
        // Store the processed image
        Storage::disk('public')->put($fullPath, $img->stream());
        
        return $fullPath;
    }
    
    /**
     * Delete an image from storage
     *
     * @param string|null $path The path to the image
     * @return bool Whether the deletion was successful
     */
    public function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }
        
        return Storage::disk('public')->delete($path);
    }
    
    /**
     * Delete responsive images from storage
     *
     * @param array $paths Array of image paths
     * @return bool Whether all deletions were successful
     */
    public function deleteResponsive(?array $paths): bool
    {
        if (!$paths || empty($paths)) {
            return false;
        }
        
        $success = true;
        
        foreach ($paths as $path) {
            if (!Storage::disk('public')->delete($path)) {
                $success = false;
            }
        }
        
        return $success;
    }
} 