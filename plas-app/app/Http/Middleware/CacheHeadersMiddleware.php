<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheHeadersMiddleware
{
    /**
     * Pages that should be heavily cached (longer duration)
     */
    protected $cacheLongPaths = [
        '/about',
        '/plans',
        '/contact'
    ];
    
    /**
     * Pages that should not be cached or have minimal cache
     */
    protected $noCachePaths = [
        '/login',
        '/register',
        '/admin/*',
    ];

    /**
     * Cache durations in seconds
     */
    protected $cacheDurations = [
        'default' => 3600,      // 1 hour
        'long' => 86400,        // 24 hours
        'short' => 300,         // 5 minutes
        'none' => 0,            // No caching
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Only apply cache headers to GET requests
        if (!$request->isMethod('GET') || !$response instanceof Response) {
            return $response;
        }
        
        // Determine cache duration based on path
        $cacheDuration = $this->getCacheDuration($request->path());
        
        // Set appropriate cache headers
        if ($cacheDuration > 0) {
            $response->headers->set('Cache-Control', 'public, max-age=' . $cacheDuration);
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + $cacheDuration) . ' GMT');
        } else {
            // No caching for protected/dynamic pages
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
        
        return $response;
    }
    
    /**
     * Determine appropriate cache duration based on the path
     * 
     * @param string $path
     * @return int Cache duration in seconds
     */
    protected function getCacheDuration(string $path): int
    {
        // Check for no-cache paths
        foreach ($this->noCachePaths as $noCache) {
            if ($this->pathMatches($path, $noCache)) {
                return $this->cacheDurations['none'];
            }
        }
        
        // Check for long-cache paths
        foreach ($this->cacheLongPaths as $longCache) {
            if ($this->pathMatches($path, $longCache)) {
                return $this->cacheDurations['long'];
            }
        }
        
        // Default cache duration
        return $this->cacheDurations['default'];
    }
    
    /**
     * Check if a path matches a pattern (with wildcard support)
     * 
     * @param string $path
     * @param string $pattern
     * @return bool
     */
    protected function pathMatches(string $path, string $pattern): bool
    {
        // Convert the pattern to a regex pattern
        $pattern = preg_quote($pattern, '/');
        $pattern = str_replace('\*', '.*', $pattern);
        
        return (bool) preg_match('/^' . $pattern . '$/', $path);
    }
} 