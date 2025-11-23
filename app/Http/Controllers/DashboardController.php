<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        switch ($request->user()->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');

            case 'instructor':
                return redirect()->route('instructor.dashboard');

            case 'member':
                return redirect()->route('member.dashboard');

            default:
                return redirect()->route('login');
        }
    }
}
