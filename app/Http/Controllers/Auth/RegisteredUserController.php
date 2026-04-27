<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        // Validate the request
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['sometimes', 'accepted'], // Optional: validate terms acceptance
        ]);

        if ($validator->fails()) {
            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }

            // Redirect back with errors for regular form submissions
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create the user with proper default values
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'member', // Set default role as member
            'notification_email' => true, // Enable email notifications by default
            'email_frequency' => 'daily', // Default email frequency
            'language' => app()->getLocale() ?? 'en', // Set user's language preference
            'timezone' => 'UTC', // Default timezone
            'theme' => 'system', // Default theme preference
            'email_verified_at' => null, // Will be set when email verification is complete
        ]);

        // Fire the registered event
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        // Regenerate session to prevent session fixation attacks
        $request->session()->regenerate();

        // Determine redirect URL based on user role
        $redirectUrl = route('member.dashboard', absolute: false);

        // For AJAX requests, return JSON response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => url($redirectUrl),
                'message' => 'Registration successful! Welcome to MyGym!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);
        }

        // For regular form submissions, redirect to member dashboard
        return redirect($redirectUrl);
    }

    /**
     * Handle API registration requests (for mobile apps or external APIs)
     */
    public function apiRegister(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'member',
                'notification_email' => true,
                'email_frequency' => 'daily',
                'language' => 'en',
                'timezone' => 'UTC',
                'theme' => 'system',
            ]);

            event(new Registered($user));

            // Create API token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registration successful!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if email already exists (for real-time validation)
     */
    public function checkEmail(Request $request): \Illuminate\Http\JsonResponse
    {
        $email = $request->input('email');

        $exists = User::where('email', $email)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Email already taken' : 'Email available'
        ]);
    }
}
