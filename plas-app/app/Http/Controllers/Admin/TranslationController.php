<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TranslationController extends Controller
{
    /**
     * The translation service instance.
     *
     * @var TranslationService
     */
    protected $translationService;

    /**
     * Create a new controller instance.
     *
     * @param TranslationService $translationService
     * @return void
     */
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
        $this->middleware('permission:manage_translations');
    }

    /**
     * Display a listing of the translations.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Translation::query()
            ->orderBy('locale')
            ->orderBy('group')
            ->orderBy('key');

        // Apply filters
        if ($request->filled('locale')) {
            $query->where('locale', $request->input('locale'));
        }

        if ($request->filled('group')) {
            $query->where('group', $request->input('group'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter')) {
            $filter = $request->input('filter');
            if ($filter === 'custom') {
                $query->where('is_custom', true);
            } elseif ($filter === 'file') {
                $query->where('is_custom', false);
            }
        }

        $translations = $query->paginate(50);
        $locales = $this->translationService->getAvailableLocales();
        
        // Get unique groups
        $groups = Translation::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return view('admin.translations.index', compact('translations', 'locales', 'groups'));
    }

    /**
     * Show the form for creating a new translation.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $locales = $this->translationService->getAvailableLocales();
        
        // Get unique groups
        $groups = Translation::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return view('admin.translations.create', compact('locales', 'groups'));
    }

    /**
     * Store a newly created translation in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'locale' => 'required|string|max:10',
            'group' => 'required|string|max:100',
            'key' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.translations.create')
                ->withErrors($validator)
                ->withInput();
        }

        $this->translationService->createOrUpdate(
            $request->input('locale'),
            $request->input('group'),
            $request->input('key'),
            $request->input('value'),
            '*',
            Auth::id()
        );

        return redirect()
            ->route('admin.translations.index')
            ->with('success', 'Translation created successfully.');
    }

    /**
     * Show the form for editing the specified translation.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $translation = Translation::findOrFail($id);
        $locales = $this->translationService->getAvailableLocales();
        
        // Get unique groups
        $groups = Translation::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return view('admin.translations.edit', compact('translation', 'locales', 'groups'));
    }

    /**
     * Update the specified translation in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'locale' => 'required|string|max:10',
            'group' => 'required|string|max:100',
            'key' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.translations.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $translation = Translation::findOrFail($id);
        
        $this->translationService->createOrUpdate(
            $request->input('locale'),
            $request->input('group'),
            $request->input('key'),
            $request->input('value'),
            $translation->namespace,
            Auth::id()
        );

        return redirect()
            ->route('admin.translations.index')
            ->with('success', 'Translation updated successfully.');
    }

    /**
     * Remove the specified translation from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $translation = Translation::findOrFail($id);
        
        $this->translationService->delete(
            $translation->locale,
            $translation->group,
            $translation->key,
            $translation->namespace
        );

        return redirect()
            ->route('admin.translations.index')
            ->with('success', 'Translation deleted successfully.');
    }

    /**
     * Import translations from files to database.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $locale = $request->filled('locale') ? $request->input('locale') : null;
        $count = $this->translationService->importTranslations($locale);

        return redirect()
            ->route('admin.translations.index')
            ->with('success', "{$count} translation files imported successfully.");
    }

    /**
     * Export translations from database to files.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function export(Request $request)
    {
        $locale = $request->filled('locale') ? $request->input('locale') : null;
        $count = $this->translationService->exportTranslations($locale);

        return redirect()
            ->route('admin.translations.index')
            ->with('success', "{$count} translation files exported successfully.");
    }
}
