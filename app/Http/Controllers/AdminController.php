<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Receipt;
use App\Models\User;
use App\Models\ScheduledClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use League\Csv\Writer;
use App\Mail\InstructorRegisteredMail;

class AdminController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Dashboard overview with comprehensive stats
     */
    public function dashboard()
    {
        // Total counts
        $totalUsers = User::count();
        $totalMembers = User::where('role', 'member')->count();
        $totalInstructors = User::where('role', 'instructor')->count();
        $totalBookings = Booking::count();
        $totalRevenue = Receipt::sum('amount') ?? 0;
        $totalClasses = ScheduledClass::count();

        // Recent data for tables
        $recentMembers = User::where('role', 'member')
            ->latest()
            ->take(5)
            ->get();

        $recentInstructors = User::where('role', 'instructor')
            ->latest()
            ->take(5)
            ->get();

        $recentBookings = Booking::with(['user', 'scheduledClass.classType'])
            ->latest()
            ->take(5)
            ->get();

        // Chart data - Last 12 months earnings
        $monthlyLabels = [];
        $monthlyEarnings = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');

            $earnings = Receipt::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');

            $monthlyEarnings[] = $earnings;
        }

        // Instructor earnings breakdown
        $instructorEarnings = User::where('role', 'instructor')
            ->with('receipts')
            ->get()
            ->map(function ($instructor) {
                return (object) [
                    'name' => $instructor->name,
                    'amount' => $instructor->receipts->sum('amount') ?? 0
                ];
            })
            ->filter(function ($item) {
                return $item->amount > 0;
            })
            ->values();

        // Plan earnings (if you have plans)
        $planEarnings = collect([]); // Add your plan earnings logic here if needed

        // All data for modals
        $allMembers = User::where('role', 'member')
            ->latest()
            ->get();

        $allInstructors = User::where('role', 'instructor')
            ->latest()
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalMembers',
            'totalInstructors',
            'totalBookings',
            'totalRevenue',
            'totalClasses',
            'recentMembers',
            'recentInstructors',
            'recentBookings',
            'monthlyLabels',
            'monthlyEarnings',
            'instructorEarnings',
            'planEarnings',
            'allMembers',
            'allInstructors'
        ));
    }

    /**
     * Earnings page with chart data
     */
    public function earnings()
    {
        $totalEarnings = Receipt::sum('amount') ?? 0;

        $monthEarnings = Receipt::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount') ?? 0;

        $pendingPayouts = 0; // Calculate based on your business logic

        $recentTransactions = Receipt::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Monthly stats for chart
        $monthlyStats = Receipt::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_key'),
                DB::raw('DATE_FORMAT(created_at, "%b") as month_label'),
                DB::raw('SUM(amount) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key', 'ASC')
            ->get();

        $monthlyLabels = $monthlyStats->pluck('month_label')->toArray();
        $monthlyEarnings = $monthlyStats->pluck('total')->toArray();

        if (empty($monthlyLabels)) {
            $monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $monthlyEarnings = array_fill(0, 12, 0);
        }

        return view('admin.earnings', compact(
            'totalEarnings',
            'monthEarnings',
            'pendingPayouts',
            'recentTransactions',
            'monthlyLabels',
            'monthlyEarnings'
        ));
    }

    /**
     * JSON endpoint for Chart.js dynamic updates
     */
    public function earningsData(Request $request)
    {
        $months = (int) $request->get('range', 12);
        $months = max(1, min(24, $months)); // Limit between 1-24 months

        $earnings = Receipt::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_key, SUM(amount) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths($months))
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get();

        return response()->json($earnings);
    }

    /**
     * Export receipts as PDF
     */
    public function exportPdf()
    {
        $receipts = Receipt::with(['user', 'scheduledClass.classType'])
            ->latest()
            ->get();

        $pdf = Pdf::loadView('admin.exports.pdf', compact('receipts'));

        return $pdf->download('receipts-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export receipts as CSV
     */
    public function exportCsv()
    {
        $receipts = Receipt::with(['user', 'scheduledClass.classType'])
            ->latest()
            ->get();

        // Create CSV using League\Csv\Writer
        $csv = Writer::createFromString();
        $csv->insertOne(['ID', 'User', 'Class', 'Payment Method', 'Amount (UGX)', 'Reference', 'Date']);

        foreach ($receipts as $receipt) {
            $csv->insertOne([
                $receipt->id,
                optional($receipt->user)->name ?? 'N/A',
                optional(optional($receipt->scheduledClass)->classType)->name ?? 'N/A',
                $receipt->payment_method,
                number_format($receipt->amount, 0),
                $receipt->reference_number ?? 'N/A',
                $receipt->created_at->format('Y-m-d'),
            ]);
        }

        $filename = 'receipts-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv->toString();
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Export all instructors as PDF
     */
    public function downloadInstructorPdf()
    {
        $instructors = User::where('role', 'instructor')
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView('admin.reports.instructors-pdf', compact('instructors'));

        return $pdf->download('instructors-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * ==================== SYSTEM MANAGEMENT METHODS ====================
     */

    /**
     * Clear system cache
     */
    public function clearSystemCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('optimize:clear');

            return redirect()->back()->with('success', 'System cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Get queue status
     */
    public function queueStatus()
    {
        try {
            $failedJobs = DB::table('failed_jobs')->count();
            $pendingJobs = DB::table('jobs')->count();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'pending_jobs' => $pendingJobs,
                    'failed_jobs' => $failedJobs
                ]);
            }

            return view('admin.system.queue-status', compact('pendingJobs', 'failedJobs'));
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to get queue status: ' . $e->getMessage());
        }
    }

    /**
     * Restart queue worker
     */
    public function restartQueue()
    {
        try {
            Artisan::call('queue:restart');

            return redirect()->back()->with('success', 'Queue worker restarted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to restart queue: ' . $e->getMessage());
        }
    }

    /**
     * System health check
     */
    public function systemHealth()
    {
        try {
            $phpVersion = phpversion();
            $laravelVersion = \Illuminate\Foundation\Application::VERSION;
            $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
            $memoryUsage = memory_get_usage(true);
            $memoryLimit = ini_get('memory_limit');
            $maxExecutionTime = ini_get('max_execution_time');
            $uploadMaxFilesize = ini_get('upload_max_filesize');
            $postMaxSize = ini_get('post_max_size');

            // Database connection check
            $dbConnection = 'Connected';
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                $dbConnection = 'Failed: ' . $e->getMessage();
            }

            return view('admin.system.health', compact(
                'phpVersion',
                'laravelVersion',
                'serverSoftware',
                'memoryUsage',
                'memoryLimit',
                'maxExecutionTime',
                'uploadMaxFilesize',
                'postMaxSize',
                'dbConnection'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to get system health: ' . $e->getMessage());
        }
    }

    /**
     * System logs
     */
    public function systemLogs()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            $logs = [];

            if (file_exists($logFile)) {
                $logContent = file_get_contents($logFile);
                $logLines = explode("\n", $logContent);
                $logs = array_reverse(array_slice($logLines, -500)); // Get last 500 lines

                // Filter out empty lines
                $logs = array_filter($logs, function($line) {
                    return !empty(trim($line));
                });
            }

            $logSize = file_exists($logFile) ? round(filesize($logFile) / 1024, 2) : 0; // Size in KB

            return view('admin.system.logs', compact('logs', 'logSize'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to get system logs: ' . $e->getMessage());
        }
    }

    /**
     * Clear logs
     */
    public function clearLogs()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                file_put_contents($logFile, '');
            }

            return redirect()->back()->with('success', 'System logs cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear logs: ' . $e->getMessage());
        }
    }

    /**
     * PHP Info
     */
    public function phpInfo()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        phpinfo();
        exit;
    }

    /**
     * Database backup
     */
    public function databaseBackup()
    {
        try {
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            $backupFile = storage_path('backups/' . $database . '_' . date('Y-m-d_H-i-s') . '.sql');

            // Create backups directory if it doesn't exist
            $backupDir = storage_path('backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Using mysqldump command
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                escapeshellarg($backupFile)
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($backupFile)) {
                return response()->download($backupFile)->deleteFileAfterSend(true);
            }

            // Alternative: Use Laravel's database backup if mysqldump fails
            if (!$file_exists($backupFile)) {
                throw new \Exception('Failed to create database backup using mysqldump');
            }

            return redirect()->back()->with('error', 'Failed to create database backup');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create database backup: ' . $e->getMessage());
        }
    }

    /**
     * Optimize database
     */
    public function optimizeDatabase()
    {
        try {
            Artisan::call('optimize');

            return redirect()->back()->with('success', 'Database optimized successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to optimize database: ' . $e->getMessage());
        }
    }

    /**
     * ==================== MEMBER CRUD ====================
     */

    /**
     * Display a listing of members
     */
    public function members()
    {
        $members = User::where('role', 'member')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.members.index', compact('members'));
    }

    /**
     * Show form to create a new member
     */
    public function createMember()
    {
        return view('admin.members.create');
    }

    /**
     * Store a newly created member
     */
    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        try {
            User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'member',
            ]);

            return redirect()->route('admin.members.index')
                ->with('success', 'Member created successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Unable to create member: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show form to edit a member
     */
    public function editMember($id)
    {
        $member = User::where('role', 'member')->findOrFail($id);
        return view('admin.members.edit', compact('member'));
    }

    /**
     * Update the specified member
     */
    public function updateMember(Request $request, $id)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        try {
            $member = User::where('role', 'member')->findOrFail($id);

            $updateData = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $member->update($updateData);

            return redirect()->route('admin.members.index')
                ->with('success', 'Member updated successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Unable to update member: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified member
     */
    public function destroyMember($id)
    {
        try {
            $member = User::where('role', 'member')->findOrFail($id);

            // Check if member has bookings before deleting
            if ($member->bookings()->exists()) {
                return redirect()->route('admin.members.index')
                    ->withErrors(['error' => 'Cannot delete member with existing bookings.']);
            }

            // Check if member has receipts
            if ($member->receipts()->exists()) {
                return redirect()->route('admin.members.index')
                    ->withErrors(['error' => 'Cannot delete member with payment history.']);
            }

            $member->delete();

            return redirect()->route('admin.members.index')
                ->with('success', 'Member deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.members.index')
                ->withErrors(['error' => 'Unable to delete member: ' . $e->getMessage()]);
        }
    }

    /**
     * ==================== INSTRUCTOR CRUD ====================
     */

    /**
     * Display a listing of instructors
     */
    public function indexInstructors()
    {
        $instructors = User::where('role', 'instructor')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.instructors.index', compact('instructors'));
    }

    /**
     * Show form to create a new instructor
     */
    public function createInstructor()
    {
        return view('admin.instructors.create');
    }

    /**
     * Store a newly created instructor
     */
    public function storeInstructor(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        try {
            $password = Str::random(12);

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($password),
                'role'     => 'instructor',
            ]);

            // Send email with password
            try {
                Mail::to($user->email)->send(new InstructorRegisteredMail($user, $password));
            } catch (\Throwable $mailError) {
                \Log::error('Failed to send instructor registration email: ' . $mailError->getMessage());
            }

            return redirect()->route('admin.instructors.index')
                ->with('success', 'Instructor registered successfully. Password has been sent to their email.');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Unable to register instructor: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show form to edit an instructor
     */
    public function editInstructor($id)
    {
        $instructor = User::where('role', 'instructor')->findOrFail($id);
        return view('admin.instructors.edit', compact('instructor'));
    }

    /**
     * Update the specified instructor
     */
    public function updateInstructor(Request $request, $id)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        try {
            $instructor = User::where('role', 'instructor')->findOrFail($id);
            $instructor->update($validated);

            return redirect()->route('admin.instructors.index')
                ->with('success', 'Instructor updated successfully.');
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['error' => 'Unable to update instructor: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified instructor
     */
    public function destroyInstructor($id)
    {
        try {
            $instructor = User::where('role', 'instructor')->findOrFail($id);

            // Check if instructor has classes before deleting
            if ($instructor->scheduledClasses()->exists()) {
                return redirect()->route('admin.instructors.index')
                    ->withErrors(['error' => 'Cannot delete instructor with existing classes.']);
            }

            // Check if instructor has receipts/earnings
            if ($instructor->receipts()->exists()) {
                return redirect()->route('admin.instructors.index')
                    ->withErrors(['error' => 'Cannot delete instructor with earnings history.']);
            }

            $instructor->delete();

            return redirect()->route('admin.instructors.index')
                ->with('success', 'Instructor deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.instructors.index')
                ->withErrors(['error' => 'Unable to delete instructor: ' . $e->getMessage()]);
        }
    }

    /**
     * Assign instructor to member
     */
    public function assignInstructor(Request $request, $memberId)
    {
        $validated = $request->validate([
            'instructor_id' => 'required|exists:users,id'
        ]);

        try {
            $member = User::where('role', 'member')->findOrFail($memberId);
            $instructor = User::where('role', 'instructor')->findOrFail($validated['instructor_id']);

            $member->instructor_id = $instructor->id;
            $member->save();

            return redirect()->back()->with('success', "{$instructor->name} assigned to {$member->name} successfully.");
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to assign instructor: ' . $e->getMessage()]);
        }
    }

    /**
     * Export members list
     */
    public function exportMembers()
    {
        $members = User::where('role', 'member')->get();

        $csv = Writer::createFromString();
        $csv->insertOne(['ID', 'Name', 'Email', 'Joined Date', 'Status']);

        foreach ($members as $member) {
            $csv->insertOne([
                $member->id,
                $member->name,
                $member->email,
                $member->created_at->format('Y-m-d'),
                $member->status ?? 'Active'
            ]);
        }

        $filename = 'members-list-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv->toString();
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Bulk delete members
     */
    public function bulkDeleteMembers(Request $request)
    {
        $validated = $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:users,id'
        ]);

        try {
            $deleted = 0;
            foreach ($validated['member_ids'] as $memberId) {
                $member = User::where('role', 'member')->find($memberId);
                if ($member && !$member->bookings()->exists() && !$member->receipts()->exists()) {
                    $member->delete();
                    $deleted++;
                }
            }

            return redirect()->back()->with('success', "{$deleted} members deleted successfully.");
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to delete members: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle instructor status
     */
    public function toggleInstructorStatus($id)
    {
        try {
            $instructor = User::where('role', 'instructor')->findOrFail($id);
            $instructor->status = $instructor->status === 'active' ? 'inactive' : 'active';
            $instructor->save();

            return redirect()->back()->with('success', "Instructor status updated to {$instructor->status}.");
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update instructor status: ' . $e->getMessage()]);
        }
    }

    /**
     * Get instructor members
     */
    public function instructorMembers($id)
    {
        $instructor = User::where('role', 'instructor')->findOrFail($id);
        $members = User::where('role', 'member')
            ->where('instructor_id', $id)
            ->get();

        return view('admin.instructors.members', compact('instructor', 'members'));
    }
}
