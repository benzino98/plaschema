<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class HealthcareProviderTemplateExport implements FromCollection, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Generate example data
        return collect([
            [
                'name' => 'Example Hospital',
                'type' => 'Hospital',
                'description' => 'A full-service medical facility providing comprehensive healthcare services.',
                'address' => '123 Health Street',
                'city' => 'Jos',
                'state' => 'Plateau',
                'phone' => '08012345678',
                'email' => 'info@examplehospital.com',
                'services' => 'Emergency Care, Surgery, Outpatient Services',
                'website' => 'https://www.examplehospital.com',
                'category' => 'Hospital',
            ],
            [
                'name' => 'Sample Clinic',
                'type' => 'Clinic',
                'description' => 'A specialized clinic focusing on primary care and preventive medicine.',
                'address' => '456 Medical Avenue',
                'city' => 'Bukuru',
                'state' => 'Plateau',
                'phone' => '09087654321',
                'email' => 'contact@sampleclinic.com',
                'services' => 'Primary Care, Vaccination, Pediatric Services',
                'website' => 'https://www.sampleclinic.com',
                'category' => 'Clinic',
            ]
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'name',
            'type',
            'description',
            'address',
            'city',
            'state',
            'phone',
            'email',
            'services',
            'website',
            'category',
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Make headers bold
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        
        // Set column widths for better readability
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(45);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(40);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(15);
        
        // Add notes about required fields
        $lastRow = $sheet->getHighestRow() + 2;
        $sheet->setCellValue('A' . $lastRow, 'Required fields: name, type, address, city, phone, email, description');
        $sheet->getStyle('A' . $lastRow)->getFont()->setBold(true);
        
        $sheet->setCellValue('A' . ($lastRow + 1), 'Note: Duplicate entries (matching name and email) will be skipped.');
        $sheet->setCellValue('A' . ($lastRow + 2), 'Note: Images cannot be imported through this template. You can add images individually after import.');
        $sheet->setCellValue('A' . ($lastRow + 3), 'Note: For services, provide a comma-separated list of services offered by the provider.');
        
        // Style notes
        $sheet->getStyle('A' . ($lastRow + 1) . ':A' . ($lastRow + 3))->getFont()->setItalic(true);
        
        return $sheet;
    }
} 