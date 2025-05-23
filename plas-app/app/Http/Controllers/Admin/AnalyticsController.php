<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\ActivityLogService;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;

class AnalyticsController extends Controller
{
    protected $analyticsService;
    protected $activityLogService;

    public function __construct(AnalyticsService $analyticsService, ActivityLogService $activityLogService)
    {
        $this->analyticsService = $analyticsService;
        $this->activityLogService = $activityLogService;
    }

    /**
     * Display the analytics dashboard
     */
    public function index()
    {
        // No need for Gate check here since we're using role middleware

        $data = $this->analyticsService->getDashboardSummary();
        
        return view('admin.analytics.dashboard', compact('data'));
    }

    /**
     * Show the report generation form
     */
    public function showReportForm()
    {
        // No need for Gate check here since we're using role middleware
        
        return view('admin.analytics.reports');
    }

    /**
     * Generate analytics report based on user selection
     */
    public function generateReport(Request $request)
    {
        // No need for Gate check here since we're using role middleware
        
        $validated = $request->validate([
            'report_type' => 'required|in:summary,providers,messages,activity',
            'format' => 'required|in:html,pdf,excel',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'nullable|exists:users,id',
        ]);
        
        $type = $validated['report_type'];
        $format = $validated['format'];
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
        $userId = $validated['user_id'] ?? null;
        
        $reportData = $this->analyticsService->generateReport(
            $type, 
            [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $userId
            ]
        );

        // Create a temporary Report object for activity logging
        $report = new Report([
            'type' => $type,
            'format' => $format,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        
        // Force an ID to make it work with activity logging
        $report->id = time();
        
        // Log the report generation activity
        $this->activityLogService->log(
            'generated',
            $report,
            "Generated {$type} report in {$format} format for period {$startDate} to {$endDate}"
        );
        
        // Return based on requested format
        if ($format === 'html') {
            // Return HTML view
            return view('admin.analytics.report_results', [
                'data' => $reportData,
                'type' => $type
            ]);
        } elseif ($format === 'pdf') {
            // Return PDF download
            $pdf = PDF::loadView('admin.analytics.pdf_report', [
                'data' => $reportData,
                'type' => $type,
                'title' => $this->getReportTitle($type),
                'period' => $startDate . ' to ' . $endDate
            ]);
            
            return $pdf->download('plaschema-' . $type . '-report.pdf');
        } else {
            // Return Excel download
            return Excel::download(
                new AnalyticsExport($reportData, $type), 
                'plaschema-' . $type . '-report.xlsx'
            );
        }
    }

    /**
     * Get report title based on report type
     */
    private function getReportTitle($type)
    {
        $titles = [
            'summary' => 'Plaschema Summary Report',
            'providers' => 'Healthcare Providers Report',
            'messages' => 'Contact Messages Report',
            'activity' => 'User Activity Report'
        ];
        
        return $titles[$type] ?? 'Plaschema Analytics Report';
    }

    /**
     * Display a listing of the resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
