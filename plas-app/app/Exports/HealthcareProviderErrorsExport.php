<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class HealthcareProviderErrorsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $errors;
    
    /**
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }
    
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = [];
        
        foreach ($this->errors as $error) {
            $errorMessages = is_array($error['errors']) ? implode(', ', $error['errors']) : $error['errors'];
            
            $data[] = [
                'row' => $error['row'] ?? 'N/A',
                'field' => $error['attribute'] ?? 'N/A',
                'error' => $errorMessages,
                'provided_value' => isset($error['values'][$error['attribute']]) ? 
                    (is_array($error['values'][$error['attribute']]) ? 
                    json_encode($error['values'][$error['attribute']]) : 
                    $error['values'][$error['attribute']]) : 'N/A'
            ];
        }
        
        return collect($data);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Row Number',
            'Field',
            'Error',
            'Provided Value'
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Add title
        $sheet->insertNewRowBefore(1, 2);
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'Import Error Report - ' . date('Y-m-d H:i:s'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // Style headers
        $sheet->getStyle('A3:D3')->getFont()->setBold(true);
        
        // Set columns width
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(30);
        
        // Add guidance
        $lastRow = $sheet->getHighestRow() + 2;
        $sheet->mergeCells("A{$lastRow}:D{$lastRow}");
        $sheet->setCellValue("A{$lastRow}", 'Please correct the errors and reupload your file.');
        $sheet->getStyle("A{$lastRow}")->getFont()->setItalic(true);
        
        return $sheet;
    }
} 