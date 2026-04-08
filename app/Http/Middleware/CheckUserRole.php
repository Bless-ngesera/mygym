<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CheckUserRole
{
    /**
     * Define role priority for dashboard redirection
     */
    protected $rolePriority = [
        'admin' => 'admin.dashboard',
        'instructor' => 'instructor.dashboard',
        'member' => 'member.dashboard',
    ];

    /**
     * Cache TTL for role checks (in seconds)
     */
    protected $cacheTTL = 3600; // 1 hour

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return $this->handleUnauthenticated($request);
        }

        $user = Auth::user();

        // Support multiple roles separated by | (pipe)
        $allowedRoles = explode('|', $role);

        // Check if user has any of the allowed roles (with caching for performance)
        $hasAccess = $this->checkUserRole($user->id, $user->role, $allowedRoles);

        if (!$hasAccess) {
            return $this->handleUnauthorized($request, $user, $allowedRoles);
        }

        return $next($request);
    }

    /**
     * Handle unauthenticated users
     */
    protected function handleUnauthenticated(Request $request): Response
    {
        Log::warning('Unauthenticated access attempt', [
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
                'code' => 'UNAUTHENTICATED'
            ], 401);
        }

        // Store the intended URL to redirect after login
        session()->put('url.intended', $request->url());

        return redirect()->route('login')->with('error', 'Please login to continue.');
    }

    /**
     * Handle unauthorized access
     */
    protected function handleUnauthorized(Request $request, $user, array $allowedRoles): Response
    {
        // Log unauthorized access attempt
        Log::warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'required_roles' => $allowedRoles,
            'path' => $request->path(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Return JSON response for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to access this resource.',
                'required_roles' => $allowedRoles,
                'your_role' => $user->role,
                'code' => 'FORBIDDEN'
            ], 403);
        }

        // Redirect to appropriate dashboard based on user's role
        return $this->redirectToDashboard($user);
    }

    /**
     * Check if user has required role (with caching)
     */
    protected function checkUserRole($userId, $userRole, array $allowedRoles): bool
    {
        // For better performance, cache the result for this user and roles
        $cacheKey = 'user_role_check_' . $userId . '_' . md5(implode('|', $allowedRoles));

        return Cache::remember($cacheKey, $this->cacheTTL, function() use ($userRole, $allowedRoles) {
            return in_array($userRole, $allowedRoles);
        });
    }

    /**
     * Redirect user to their appropriate dashboard based on role
     */
    protected function redirectToDashboard($user): Response
    {
        // If user has no role, redirect to home
        if (!$user->role) {
            return redirect()->route('home')->with('error', 'Access denied. No role assigned. Please contact support.');
        }

        // Check if the user's role has a defined dashboard route
        if (isset($this->rolePriority[$user->role])) {
            return redirect()->route($this->rolePriority[$user->role])
                ->with('error', 'You do not have permission to access that page.');
        }

        // Fallback redirect for any other role
        return redirect()->route('dashboard')->with('error', 'Unauthorized access. Please contact support if you believe this is an error.');
    }

    /**
     * Clear role cache for a specific user (useful after role changes)
     */
    public static function clearRoleCache($userId)
    {
        // This method can be called when user roles are updated
        $pattern = 'user_role_check_' . $userId . '_*';
        $keys = Cache::get($pattern);
        if ($keys) {
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }
}
