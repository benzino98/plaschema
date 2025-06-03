<?php

namespace App\Imports;

use App\Models\HealthcareProvider;
use App\Services\ActivityLogService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class HealthcareProvidersImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable;
    
    protected $activityLogService;
    protected $errors = [];
    protected $imported = 0;
    protected $skipped = 0;
    
    /**
     * @param ActivityLogService $activityLogService
     */
    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Check for duplicates
            $existing = HealthcareProvider::where('name', $row['name'])
                ->where('email', $row['email'])
                ->first();
                
            if ($existing) {
                $this->skipped++;
                continue;
            }

            // Convert services to JSON if it's provided as a string
            $services = $row['services'] ?? null;
            if (is_string($services) && !empty($services)) {
                $services = explode(',', $services);
                $services = array_map('trim', $services);
            }
            
            // Create new provider
            $provider = HealthcareProvider::create([
                'name' => $row['name'],
                'type' => $row['type'],
                'description' => $row['description'],
                'address' => $row['address'],
                'city' => $row['city'],
                'state' => $row['state'] ?? 'Plateau',
                'phone' => $row['phone'],
                'email' => $row['email'],
                'services' => $services,
                'website' => $row['website'] ?? null,
                'status' => 'active',
                'category' => $row['category'] ?? null,
            ]);
            
            // Log activity
            $this->activityLogService->logCreated($provider, ['import' => true]);
            
            $this->imported++;
        }
    }
    
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'type' => 'required|max:100',
            'address' => 'required|max:255',
            'city' => 'required|max:100',
            'state' => 'nullable|max:100',
            'phone' => 'required|max:50',
            'email' => 'required|email|max:255',
            'description' => 'required',
            'services' => 'nullable',
            'website' => 'nullable|url|max:255',
            'category' => 'nullable|max:100',
        ];
    }

    /**
     * @param Throwable $e
     */
    public function onError(Throwable $e)
    {
        $this->errors[] = [
            'row' => null,
            'attribute' => 'general',
            'errors' => [$e->getMessage()],
            'values' => []
        ];
    }
    
    /**
     * @param Failure ...$failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }
    
    /**
     * Get all validation errors
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get count of imported records
     * 
     * @return int
     */
    public function getImportedCount(): int
    {
        return $this->imported;
    }
    
    /**
     * Get count of skipped records
     * 
     * @return int
     */
    public function getSkippedCount(): int
    {
        return $this->skipped;
    }
} 