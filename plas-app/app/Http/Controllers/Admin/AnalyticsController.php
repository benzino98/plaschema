<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the analytics dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('view-analytics');

        $data = $this->analyticsService->getDashboardSummary();
        
        return view('admin.analytics.dashboard', compact('data'));
    }

    /**
     * Show the report generation form
     *
     * @return \Illuminate\Http\Response
     */
    public function showReportForm()
    {
        Gate::authorize('view-analytics');
        
        return view('admin.analytics.reports');
    }

    /**
     * Generate a report based on form input
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(Request $request)
    {
        Gate::authorize('view-analytics');
        
        $validated = $request->validate([
            'report_type' => 'required|in:summary,providers,messages,activity',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:html,pdf,excel',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $options = [
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ];

        if (isset($validated['user_id'])) {
            $options['user_id'] = $validated['user_id'];
        }

        $reportData = $this->analyticsService->generateReport(
            $validated['report_type'],
            $options
        );

        // Get report title based on type
        $reportTitle = ucfirst($validated['report_type']) . ' Report';
        $period = Carbon::parse($options['start_date'])->format('M d, Y') . ' - ' . 
                 Carbon::parse($options['end_date'])->format('M d, Y');

        // Return the appropriate format
        switch ($validated['format']) {
            case 'pdf':
                return $this->generatePdfReport($reportTitle, $period, $reportData, $validated['report_type']);
            case 'excel':
                return $this->generateExcelReport($reportTitle, $period, $reportData, $validated['report_type']);
            default:
                return view('admin.analytics.report_results', [
                    'title' => $reportTitle,
                    'period' => $period,
                    'data' => $reportData,
                    'type' => $validated['report_type']
                ]);
        }
    }

    /**
     * Generate a PDF report
     *
     * @param string $title
     * @param string $period
     * @param array $data
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    protected function generatePdfReport($title, $period, $data, $type)
    {
        $pdf = PDF::loadView('admin.analytics.pdf_report', [
            'title' => $title,
            'period' => $period,
            'data' => $data,
            'type' => $type
        ]);

        $filename = strtolower(str_replace(' ', '_', $title)) . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate an Excel report
     *
     * @param string $title
     * @param string $period
     * @param array $data
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    protected function generateExcelReport($title, $period, $data, $type)
    {
        $filename = strtolower(str_replace(' ', '_', $title)) . '_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new ReportExport($title, $period, $data, $type), $filename);
    }
} 