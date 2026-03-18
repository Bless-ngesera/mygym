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
                // Log mail error but don't stop the process
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
}
