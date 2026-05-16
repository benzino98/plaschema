<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        // Use the new admin login view for all login requests
        return view('auth.admin-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Check if user has admin role and redirect accordingly
        $user = Auth::user();
        
        // Debug logs removed for clarity
        
        if ($user && $user->hasAnyAdminRole()) {
            $intended = session()->pull('url.intended');

            if ($intended && str_contains($intended, '/admin')) {
                return redirect($intended);
            }

            return redirect()->route('admin.dashboard');
        }

        // Valid credentials but no admin role — log out so the login page can show an error
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'This account does not have admin access. Ask a super admin to assign you a role, or use the server create-admin utility.',
            ])
            ->withInput($request->only('email'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
