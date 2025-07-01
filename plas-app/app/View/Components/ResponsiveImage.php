<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ResponsiveImage extends Component
{
    /**
     * Image attributes
     */
    public $pathSmall;
    public $pathMedium;
    public $pathLarge;
    public $pathOriginal;
    public $alt;
    public $class;
    public $loading;
    public $additionalAttributes;

    /**
     * Helper method to format image path correctly
     * 
     * @param string|null $path
     * @return string|null
     */
    protected function formatImagePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        
        // If it's already a full URL, return as is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        
        // If it already has storage/ prefix, return as is
        if (str_starts_with($path, 'storage/') || str_starts_with($path, '/storage/')) {
            return $path;
        }
        
        // Add storage/ prefix to the path
        return 'storage/' . $path;
    }

    /**
     * Create a new component instance.
     * 
     * @param string|null $pathSmall Path to small version of image
     * @param string|null $pathMedium Path to medium version of image
     * @param string|null $pathLarge Path to large version of image
     * @param string|null $pathOriginal Path to original version of image
     * @param string $alt Alt text for the image
     * @param string $class CSS classes to apply to the image
     * @param string $loading Loading attribute value (lazy, eager, auto)
     * @param array $additionalAttributes Additional attributes to apply to the image tag
     */
    public function __construct(
        ?string $pathSmall = null,
        ?string $pathMedium = null,
        ?string $pathLarge = null,
        ?string $pathOriginal = null,
        string $alt = '',
        string $class = '',
        string $loading = 'lazy',
        array $additionalAttributes = []
    ) {
        $this->pathSmall = $this->formatImagePath($pathSmall);
        $this->pathMedium = $this->formatImagePath($pathMedium);
        $this->pathLarge = $this->formatImagePath($pathLarge);
        $this->pathOriginal = $this->formatImagePath($pathOriginal);
        $this->alt = $alt;
        $this->class = $class;
        $this->loading = $loading;
        $this->additionalAttributes = $additionalAttributes;
    }

    /**
     * Determine if there are enough sources for srcset
     */
    public function hasSrcset(): bool
    {
        return ($this->pathSmall || $this->pathMedium || $this->pathLarge) && $this->pathOriginal;
    }

    /**
     * Generate the srcset attribute value
     */
    public function getSrcset(): string
    {
        $srcset = [];
        
        if ($this->pathSmall) {
            $srcset[] = "{$this->pathSmall} 400w";
        }
        
        if ($this->pathMedium) {
            $srcset[] = "{$this->pathMedium} 800w";
        }
        
        if ($this->pathLarge) {
            $srcset[] = "{$this->pathLarge} 1200w";
        }
        
        return implode(', ', $srcset);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.responsive-image');
    }
}
