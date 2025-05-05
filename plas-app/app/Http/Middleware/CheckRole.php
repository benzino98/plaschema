<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Multiple roles can be passed
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Log for debugging
        Log::info('CheckRole middleware', [
            'user_id' => $user->id,
            'roles_required' => $roles,
            'uri' => $request->getRequestUri()
        ]);
        
        // Handle multiple comma-separated roles
        $roleList = [];
        foreach ($roles as $role) {
            if (str_contains($role, ',')) {
                $roleList = array_merge($roleList, explode(',', $role));
            } else {
                $roleList[] = $role;
            }
        }
        
        // Check if user has any of the required roles
        $hasRole = false;
        foreach ($roleList as $role) {
            if ($user->hasRole(trim($role))) {
                $hasRole = true;
                break;
            }
        }
        
        if (!$hasRole) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
