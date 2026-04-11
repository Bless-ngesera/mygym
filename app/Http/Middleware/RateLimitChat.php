<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitChat
{
    /**
     * The rate limiter instance.
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Cache\RateLimiter  $limiter
     * @return void
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $maxAttempts
     * @param  int  $decayMinutes
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 20, $decayMinutes = 1)
    {
        // Only apply rate limiting to authenticated users and chat endpoints
        if ($request->user() && $request->is('chat/*')) {
            $key = 'chat:' . $request->user()->id;

            // Check if user has exceeded rate limit
            if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
                $retryAfter = $this->limiter->availableIn($key);

                return response()->json([
                    'success' => false,
                    'error' => 'Too many messages',
                    'message' => "You have exceeded the rate limit. Please wait {$retryAfter} seconds before sending more messages.",
                    'retry_after' => $retryAfter
                ], 429);
            }

            // Increment the rate limit counter
            $this->limiter->hit($key, $decayMinutes * 60);
        }

        return $next($request);
    }
}
