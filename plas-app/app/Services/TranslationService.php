<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TranslationService
{
    /**
     * Cache duration in seconds (24 hours)
     */
    protected const CACHE_DURATION = 86400;

    /**
     * Get a translation for a specific key
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @param bool $fallback
     * @return string
     */
    public function get(string $key, array $replace = [], ?string $locale = null, bool $fallback = true): string
    {
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }

        // Parse key in format: namespace::group.key or group.key
        list($namespace, $group, $item) = $this->parseKey($key);

        // Try to get the translation from cache first
        $cacheKey = "translation.{$locale}.{$namespace}.{$group}.{$item}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($namespace, $group, $item, $locale, $fallback) {
            // Try to get from database
            $translation = $this->getFromDatabase($namespace, $group, $item, $locale);
            
            if ($translation) {
                // Update usage timestamp
                $this->updateUsageTimestamp($translation);
                return $translation->value;
            }
            
            // Try to get from file
            $fromFile = $this->getFromFile($namespace, $group, $item, $locale);
            
            if ($fromFile) {
                return $fromFile;
            }
            
            // Try fallback locale if enabled
            if ($fallback && $locale !== config('app.fallback_locale')) {
                return $this->get("{$namespace}::{$group}.{$item}", [], config('app.fallback_locale'), false);
            }
            
            // Return the key as a last resort
            return $key;
        });
    }

    /**
     * Parse a translation key
     *
     * @param string $key
     * @return array
     */
    protected function parseKey(string $key): array
    {
        $namespace = '*';
        $group = '';
        $item = '';

        if (strpos($key, '::') !== false) {
            list($namespace, $key) = explode('::', $key);
        }

        if (strpos($key, '.') !== false) {
            list($group, $item) = explode('.', $key, 2);
        } else {
            $group = $key;
        }

        return [$namespace, $group, $item];
    }

    /**
     * Get a translation from the database
     *
     * @param string $namespace
     * @param string $group
     * @param string $item
     * @param string $locale
     * @return Translation|null
     */
    protected function getFromDatabase(string $namespace, string $group, string $item, string $locale): ?Translation
    {
        return Translation::where('locale', $locale)
            ->where('namespace', $namespace)
            ->where('group', $group)
            ->where('key', $item)
            ->first();
    }

    /**
     * Update the usage timestamp for a translation
     *
     * @param Translation $translation
     * @return void
     */
    protected function updateUsageTimestamp(Translation $translation): void
    {
        // Only update the timestamp once per day to avoid excessive database writes
        if (!$translation->last_used_at || $translation->last_used_at->diffInDays(now()) >= 1) {
            $translation->markAsUsed();
        }
    }

    /**
     * Get a translation from file
     *
     * @param string $namespace
     * @param string $group
     * @param string $item
     * @param string $locale
     * @return string|null
     */
    protected function getFromFile(string $namespace, string $group, string $item, string $locale): ?string
    {
        $path = resource_path("lang/{$locale}/{$group}.php");
        
        if (!File::exists($path)) {
            return null;
        }
        
        $translations = include $path;
        
        return $translations[$item] ?? null;
    }

    /**
     * Create or update a translation
     *
     * @param string $locale
     * @param string $group
     * @param string $key
     * @param string $value
     * @param string $namespace
     * @param int|null $userId
     * @return Translation
     */
    public function createOrUpdate(string $locale, string $group, string $key, string $value, string $namespace = '*', ?int $userId = null): Translation
    {
        $data = [
            'locale' => $locale,
            'group' => $group,
            'key' => $key,
            'value' => $value,
            'namespace' => $namespace,
            'is_custom' => true,
        ];
        
        if ($userId) {
            $data['updated_by'] = $userId;
        }
        
        $translation = Translation::updateOrCreate(
            [
                'locale' => $locale,
                'namespace' => $namespace,
                'group' => $group,
                'key' => $key,
            ],
            $data
        );
        
        // If this is a new record, set created_by
        if ($userId && $translation->wasRecentlyCreated) {
            $translation->created_by = $userId;
            $translation->save();
        }
        
        // Clear the cache for this translation
        $this->clearTranslationCache($locale, $namespace, $group, $key);
        
        return $translation;
    }

    /**
     * Delete a translation
     *
     * @param string $locale
     * @param string $group
     * @param string $key
     * @param string $namespace
     * @return bool
     */
    public function delete(string $locale, string $group, string $key, string $namespace = '*'): bool
    {
        $result = Translation::where('locale', $locale)
            ->where('namespace', $namespace)
            ->where('group', $group)
            ->where('key', $key)
            ->delete();
        
        // Clear the cache
        $this->clearTranslationCache($locale, $namespace, $group, $key);
        
        return $result > 0;
    }

    /**
     * Clear the cache for a specific translation
     *
     * @param string $locale
     * @param string $namespace
     * @param string $group
     * @param string $key
     * @return void
     */
    protected function clearTranslationCache(string $locale, string $namespace, string $group, string $key): void
    {
        $cacheKey = "translation.{$locale}.{$namespace}.{$group}.{$key}";
        Cache::forget($cacheKey);
    }

    /**
     * Get all available locales
     *
     * @return array
     */
    public function getAvailableLocales(): array
    {
        $locales = [];
        
        // Get locales from language files
        $langPath = resource_path('lang');
        
        if (File::exists($langPath)) {
            $directories = File::directories($langPath);
            
            foreach ($directories as $directory) {
                $locales[] = basename($directory);
            }
        }
        
        // Get locales from database
        $dbLocales = Translation::select('locale')
            ->distinct()
            ->pluck('locale')
            ->toArray();
        
        return array_unique(array_merge($locales, $dbLocales));
    }

    /**
     * Import translations from files to database
     *
     * @param string|null $locale
     * @return int
     */
    public function importTranslations(?string $locale = null): int
    {
        $importCount = 0;
        $langPath = resource_path('lang');
        
        if (!File::exists($langPath)) {
            return 0;
        }
        
        $locales = $locale ? [$locale] : array_map('basename', File::directories($langPath));
        
        foreach ($locales as $locale) {
            $localePath = "{$langPath}/{$locale}";
            
            if (!File::exists($localePath)) {
                continue;
            }
            
            $files = File::files($localePath);
            
            foreach ($files as $file) {
                $group = pathinfo($file, PATHINFO_FILENAME);
                $translations = include $file->getPathname();
                
                $this->importGroupTranslations($locale, $group, $translations);
                $importCount++;
            }
        }
        
        return $importCount;
    }

    /**
     * Import a group of translations
     *
     * @param string $locale
     * @param string $group
     * @param array $translations
     * @param string $namespace
     * @param string $keyPrefix
     * @return void
     */
    protected function importGroupTranslations(string $locale, string $group, array $translations, string $namespace = '*', string $keyPrefix = ''): void
    {
        foreach ($translations as $key => $value) {
            $fullKey = $keyPrefix ? "{$keyPrefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $this->importGroupTranslations($locale, $group, $value, $namespace, $fullKey);
            } else {
                // Skip if a custom translation already exists
                $existing = Translation::where('locale', $locale)
                    ->where('namespace', $namespace)
                    ->where('group', $group)
                    ->where('key', $fullKey)
                    ->where('is_custom', true)
                    ->first();
                
                if (!$existing) {
                    Translation::updateOrCreate(
                        [
                            'locale' => $locale,
                            'namespace' => $namespace,
                            'group' => $group,
                            'key' => $fullKey,
                        ],
                        [
                            'value' => $value,
                            'is_custom' => false,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Export translations from database to files
     *
     * @param string|null $locale
     * @return int
     */
    public function exportTranslations(?string $locale = null): int
    {
        $exportCount = 0;
        
        $query = Translation::query();
        
        if ($locale) {
            $query->where('locale', $locale);
        }
        
        $translations = $query->get()
            ->groupBy(['locale', 'group']);
        
        foreach ($translations as $locale => $groups) {
            foreach ($groups as $group => $items) {
                $path = resource_path("lang/{$locale}");
                
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                }
                
                $exportData = [];
                
                foreach ($items as $item) {
                    $exportData[$item->key] = $item->value;
                }
                
                if (!empty($exportData)) {
                    $content = "<?php\n\nreturn " . var_export($exportData, true) . ";\n";
                    File::put("{$path}/{$group}.php", $content);
                    $exportCount++;
                }
            }
        }
        
        return $exportCount;
    }
} 