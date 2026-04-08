<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'instructor') {
            return redirect()->route('instructor.dashboard');
        }

        if ($user->role === 'member') {
            return redirect()->route('member.dashboard');
        }

        return redirect()->route('login')->with('error', 'Invalid user role.');
    }
}
