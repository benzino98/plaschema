<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\HealthcareProvider;
use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class GenerateResponsiveImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-responsive-images {--model=all : Model to process (news, providers, or all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate responsive versions of existing images';

    /**
     * Responsive image size configurations
     */
    protected $sizes = [
        'small' => ['width' => 400, 'height' => null],
        'medium' => ['width' => 800, 'height' => null],
        'large' => ['width' => 1200, 'height' => null],
    ];

    /**
     * Execute the console command.
     */
    public function handle(ImageService $imageService)
    {
        $model = $this->option('model');
        
        $this->info('Starting responsive image generation...');
        
        if ($model === 'all' || $model === 'news') {
            $this->processNewsImages($imageService);
        }
        
        if ($model === 'all' || $model === 'providers') {
            $this->processProviderImages($imageService);
        }
        
        $this->info('Responsive image generation completed!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Process news images
     */
    protected function processNewsImages(ImageService $imageService)
    {
        $this->info('Processing news images...');
        $bar = $this->output->createProgressBar(News::count());
        $bar->start();
        
        News::chunk(10, function ($newsItems) use ($bar, $imageService) {
            foreach ($newsItems as $news) {
                if ($news->image_path) {
                    $this->generateResponsiveVersions($news, 'image_path', 'news');
                }
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->newLine();
        $this->info('News images processed!');
    }
    
    /**
     * Process healthcare provider images
     */
    protected function processProviderImages(ImageService $imageService)
    {
        $this->info('Processing healthcare provider images...');
        $bar = $this->output->createProgressBar(HealthcareProvider::count());
        $bar->start();
        
        HealthcareProvider::chunk(10, function ($providers) use ($bar, $imageService) {
            foreach ($providers as $provider) {
                if ($provider->logo_path) {
                    $this->generateResponsiveVersions($provider, 'logo_path', 'providers');
                }
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->newLine();
        $this->info('Healthcare provider images processed!');
    }
    
    /**
     * Generate responsive versions of an image
     */
    protected function generateResponsiveVersions($model, $fieldName, $storagePath)
    {
        $imagePath = $model->$fieldName;
        
        // Skip if image doesn't exist
        if (!$imagePath || !Storage::disk('public')->exists($imagePath)) {
            return;
        }
        
        try {
            // Get the original image
            $originalImage = Storage::disk('public')->get($imagePath);
            
            // Generate a base filename without extension
            $pathInfo = pathinfo($imagePath);
            $extension = $pathInfo['extension'] ?? 'jpg';
            $baseFilename = Str::uuid();
            
            // Process each size
            foreach ($this->sizes as $sizeName => $dimensions) {
                $sizeFieldName = "{$fieldName}_{$sizeName}";
                
                // Skip if already processed
                if ($model->$sizeFieldName) {
                    continue;
                }
                
                // Create the image with the specified size
                $img = Image::make($originalImage);
                $img->resize($dimensions['width'], $dimensions['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                // Generate new filename and path
                $newFilename = "{$baseFilename}_{$sizeName}.{$extension}";
                $newPath = $storagePath . '/' . $newFilename;
                
                // Save to storage
                Storage::disk('public')->put($newPath, $img->encode($extension, 80));
                
                // Update model
                $model->$sizeFieldName = $newPath;
            }
            
            // Save the model with the new fields
            $model->save();
            
        } catch (\Exception $e) {
            $this->error("Error processing image {$imagePath}: " . $e->getMessage());
        }
    }
}
