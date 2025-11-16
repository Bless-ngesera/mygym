<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index() {
    return view('admin.dashboard', [
        'membersCount' => \App\Models\User::where('role', 'member')->count(),
        'instructorsCount' => \App\Models\User::where('role', 'instructor')->count(),
        'adminsCount' => \App\Models\User::where('role', 'admin')->count(),
        'recentMembers' => \App\Models\User::where('role', 'member')->latest()->take(5)->get(),
        'recentInstructors' => \App\Models\User::where('role', 'instructor')->latest()->take(5)->get(),
    ]);
}

}
