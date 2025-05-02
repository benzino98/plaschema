<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Process and store an uploaded image with optimization
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
} 