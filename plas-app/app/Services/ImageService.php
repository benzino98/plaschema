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
                
                // Optimize the image (reduce quality slightly to save space)
                $img->encode($extension, 80);
                
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
        $originalImg->encode($extension, 90);
        Storage::disk('public')->put($originalFullPath, $originalImg->stream());
        
        $result['original'] = $originalFullPath;
        
        return $result;
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
        
        // Optimize the image (reduce quality slightly to save space)
        $img->encode($image->getClientOriginalExtension(), 80);
        
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