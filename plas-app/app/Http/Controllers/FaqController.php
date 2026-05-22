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
        $categories = Faq::active()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->orderBy('category')
            ->pluck('category');

        $query = Faq::active()->ordered();

        if ($request->filled('category')) {
            $query->category($request->category);
        }

        $faqs = $query->get();

        return view('pages.faq', [
            'faqs' => $faqs,
            'categories' => $categories,
            'currentCategory' => $request->category,
        ]);
    }
}
