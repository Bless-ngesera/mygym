<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Receipt;
use App\Models\User;
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
     * Dashboard overview (recent members + plan income summary included)
     */
    public function dashboard()
    {
        $totalBookings   = Booking::count();
        $totalRevenue    = Receipt::sum('amount');
        $recentBookings  = Booking::latest()->take(5)->get();

        // Recent members
        $recentMembers = User::where('role', 'member')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();


        // Recent instructors
        $recentInstructors = User::where('role', 'instructor')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'totalBookings'    => $totalBookings,
            'totalRevenue'     => $totalRevenue,
            'recentBookings'   => $recentBookings,
            'recentMembers'    => $recentMembers,
            'recentInstructors'=> $recentInstructors,
        ]);
    }

    /**
     * Earnings page with chart data
     */
    public function earnings()
    {
        $totalEarnings = Receipt::sum('amount');

        $monthEarnings = Receipt::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $pendingPayouts = 0;

        $recentTransactions = Receipt::with('user')->latest()->take(10)->get();

        $monthlyStats = Receipt::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_key'),
                DB::raw('DATE_FORMAT(created_at, "%b") as month_label'),
                DB::raw('SUM(amount) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key', 'ASC')
            ->get();

        $monthlyLabels   = $monthlyStats->pluck('month_label')->toArray();
        $monthlyEarnings = $monthlyStats->pluck('total')->toArray();

        if (empty($monthlyLabels)) {
            $monthlyLabels   = ['Jan', 'Feb', 'Mar', 'Apr', 'May'];
            $monthlyEarnings = [0, 0, 0, 0, 0];
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
     * JSON endpoint for Chart.js
     */
    public function earningsData(Request $request)
    {
        $months = (int) $request->get('range', 12);
        $months = $months > 0 ? $months : 12;

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
        $receipts = Receipt::with(['user', 'scheduledClass.classType'])->latest()->get();

        $pdf = Pdf::loadView('admin.exports.pdf', [
            'receipts' => $receipts
        ]);

        return $pdf->download('receipts-report.pdf');
    }

    /**
     * Export receipts as CSV
     */
    public function exportCsv()
    {
        $receipts = Receipt::with(['user', 'scheduledClass.classType'])->latest()->get();

        $csv = Writer::createFromString('');
        $csv->insertOne(['ID', 'User', 'Class', 'Payment Method', 'Amount', 'Reference', 'Date']);

        foreach ($receipts as $receipt) {
            $csv->insertOne([
                $receipt->id,
                optional($receipt->user)->name ?? 'N/A',
                optional(optional($receipt->scheduledClass)->classType)->name ?? 'N/A',
                $receipt->payment_method,
                $receipt->amount,
                $receipt->reference_number ?? '',
                $receipt->created_at->format('Y-m-d'),
            ]);
        }

        $filename = 'receipts-report.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv->toString();
        }, $filename, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Instructor CRUD
     */
    public function indexInstructors()
    {
        $instructors = User::where('role', 'instructor')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.instructors.index', compact('instructors'));
    }

    public function createInstructor()
    {
        return view('admin.instructors.create');
    }

    public function storeInstructor(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        try {
            $user = new User();
            $user->name     = $validated['name'];
            $user->email    = $validated['email'];
            $user->password = Hash::make(Str::random(12));
            $user->role     = 'instructor';
            $user->save();

            Mail::to($user->email)->send(new InstructorRegisteredMail($user));

            return back()->with('success', 'Instructor registered successfully and email sent.');
        } catch (\Throwable $e) {
            return back()->withErrors(['msg' => 'Unable to register instructor.'])->withInput();
        }
    }

    public function editInstructor($id)
    {
        $instructor = User::where('role', 'instructor')->findOrFail($id);
        return view('admin.instructors.edit', compact('instructor'));
    }

    public function updateInstructor(Request $request, $id)
{
    $validated = $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
    ]);

    $instructor = User::where('role', 'instructor')->findOrFail($id);
    $instructor->update($validated);

    return redirect()->route('admin.dashboard')
        ->with('success', 'Instructor updated successfully.');
}

public function destroyInstructor($id)
{
    try {
        $instructor = User::where('role', 'instructor')->findOrFail($id);
        $instructor->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Instructor deleted successfully.');
    } catch (\Throwable $e) {
        return redirect()->route('admin.dashboard')
            ->withErrors(['msg' => 'Unable to delete instructor.']);
    }
}

public function downloadInstructorPdf()
{
    $instructors = User::where('role', 'instructor')
        ->orderByDesc('created_at')
        ->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.instructors-pdf', [
        'instructors' => $instructors
    ]);

    return $pdf->download('instructors-report.pdf');
}


}
