<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $title;
    protected $period;
    protected $data;
    protected $type;

    /**
     * @param string $title
     * @param string $period
     * @param array $data
     * @param string $type
     */
    public function __construct($title, $period, $data, $type)
    {
        $this->title = $title;
        $this->period = $period;
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Transform report data into collection format based on report type
        switch ($this->type) {
            case 'summary':
                return $this->formatSummaryReport();
            case 'providers':
                return $this->formatProviderReport();
            case 'messages':
                return $this->formatMessageReport();
            case 'activity':
                return $this->formatActivityReport();
            default:
                return new Collection([]);
        }
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        switch ($this->type) {
            case 'summary':
                return ['Category', 'Item', 'Count'];
            case 'providers':
                return ['Category', 'Type', 'Count'];
            case 'messages':
                return ['Category', 'Status', 'Count'];
            case 'activity':
                return ['User', 'Action', 'Entity Type', 'Count'];
            default:
                return [];
        }
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title . ' (' . $this->period . ')';
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getFont()->setSize(14);
        $sheet->getStyle('A2:C2')->getFont()->setSize(12);
        $sheet->getStyle('A2:C2')->getFont()->setItalic(true);
        
        // Add title
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A1', $this->title);
        
        // Add period
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('A2', 'Period: ' . $this->period);
        
        // Set columns width
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        
        // Move content down
        $sheet->insertNewRowBefore(3, 2);
    }

    /**
     * Format summary report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function formatSummaryReport()
    {
        $collection = new Collection();
        
        // Content section
        foreach ($this->data['content'] as $key => $value) {
            $collection->push(['Content', $this->formatKey($key), $value]);
        }
        
        // Messages section
        $collection->push(['Messages', 'Total', $this->data['messages']['total']]);
        foreach ($this->data['messages']['by_status'] as $status => $count) {
            $collection->push(['Messages', 'Status: ' . ucfirst($status), $count]);
        }
        
        // Activity section
        $collection->push(['Activity', 'Total', $this->data['activity']['total']]);
        foreach ($this->data['activity']['by_action'] as $action => $count) {
            $collection->push(['Activity', 'Action: ' . ucfirst($action), $count]);
        }
        
        return $collection;
    }

    /**
     * Format provider report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function formatProviderReport()
    {
        $collection = new Collection();
        
        // Total
        $collection->push(['All Categories', 'All Types', $this->data['total']]);
        
        // By category
        foreach ($this->data['by_category'] as $category => $count) {
            $collection->push([$category, 'All Types', $count]);
        }
        
        // By type
        foreach ($this->data['by_type'] as $type => $count) {
            $collection->push(['All Categories', $type, $count]);
        }
        
        // By city
        foreach ($this->data['by_city'] as $city => $count) {
            $collection->push(['City', $city, $count]);
        }
        
        return $collection;
    }

    /**
     * Format message report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function formatMessageReport()
    {
        $collection = new Collection();
        
        // Total
        $collection->push(['All Categories', 'All Statuses', $this->data['total']]);
        
        // By category
        foreach ($this->data['by_category'] as $category => $count) {
            $collection->push([$category, 'All Statuses', $count]);
        }
        
        // By status
        foreach ($this->data['by_status'] as $status => $count) {
            $collection->push(['All Categories', ucfirst($status), $count]);
        }
        
        // Response times
        $collection->push(['Response Times', 'Average (hours)', $this->data['response_times']['average_hours']]);
        $collection->push(['Response Times', 'Minimum (hours)', $this->data['response_times']['min_hours']]);
        $collection->push(['Response Times', 'Maximum (hours)', $this->data['response_times']['max_hours']]);
        
        // Daily counts
        foreach ($this->data['daily_counts'] as $date => $count) {
            $collection->push(['Daily', Carbon::parse($date)->format('M d, Y'), $count]);
        }
        
        return $collection;
    }

    /**
     * Format activity report data
     *
     * @return \Illuminate\Support\Collection
     */
    protected function formatActivityReport()
    {
        $collection = new Collection();
        
        // Total
        $collection->push(['All Users', 'All Actions', 'All Entities', $this->data['total']]);
        
        // By user
        foreach ($this->data['by_user'] as $user => $count) {
            $collection->push([$user, 'All Actions', 'All Entities', $count]);
        }
        
        // By action
        foreach ($this->data['by_action'] as $action => $count) {
            $collection->push(['All Users', ucfirst($action), 'All Entities', $count]);
        }
        
        // By entity type
        foreach ($this->data['by_entity_type'] as $entityType => $count) {
            $collection->push(['All Users', 'All Actions', ucfirst($entityType), $count]);
        }
        
        // Daily counts
        foreach ($this->data['daily_counts'] as $date => $count) {
            $collection->push(['Daily', Carbon::parse($date)->format('M d, Y'), 'All Entities', $count]);
        }
        
        return $collection;
    }

    /**
     * Format key for display
     *
     * @param string $key
     * @return string
     */
    protected function formatKey($key)
    {
        return ucfirst(str_replace('_', ' ', $key));
    }
} 