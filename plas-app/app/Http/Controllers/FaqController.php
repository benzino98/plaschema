<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the FAQs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Faq::active()->ordered();
        
        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->category($request->category);
        }
        
        // Get all categories for filter tabs
        $categories = Faq::active()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');
            
        $faqs = $query->get();
        
        return view('pages.faq', [
            'faqs' => $faqs,
            'categories' => $categories,
            'currentCategory' => $request->category,
        ]);
    }
}
