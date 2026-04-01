<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Receipt;
use App\Models\ScheduledClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the user's receipts.
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view your receipts.');
        }

        // Get all receipts for the logged-in user
        $receipts = Receipt::where('user_id', $user->id)
            ->with(['scheduledClass.classType', 'scheduledClass.instructor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('receipts.index', compact('receipts'));
    }

    /**
     * Store a newly created receipt and create booking.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'scheduled_class_id' => 'required|integer|exists:scheduled_classes,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'payment_contact' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Check if user is a member
        if ($user->role !== 'member') {
            return redirect()->back()->with('error', 'Only members can book classes.');
        }

        // Get the scheduled class
        $scheduledClass = ScheduledClass::findOrFail($data['scheduled_class_id']);

        // Check if the class is in the future
        if ($scheduledClass->date_time->isPast()) {
            return redirect()->back()->with('error', 'Cannot book past classes.');
        }

        // Check if already booked
        $existingBooking = $user->bookings()->where('scheduled_class_id', $scheduledClass->id)->exists();
        if ($existingBooking) {
            return redirect()->back()->with('error', 'You already booked this class.');
        }

        try {
            // Start transaction
            DB::beginTransaction();

            // Create receipt record
            $receipt = Receipt::create([
                'user_id' => $user->id,
                'scheduled_class_id' => $data['scheduled_class_id'],
                'payment_method' => $data['payment_method'],
                'amount' => $data['amount'],
                'reference_number' => 'RCP-' . strtoupper(uniqid()) . '-' . date('Ymd'),
                'payment_contact' => $data['payment_contact'] ?? null,
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            // Create booking for the class
            $user->bookings()->attach($scheduledClass->id);

            // Commit transaction
            DB::commit();

            // Ensure relations are loaded for the view
            $receipt->loadMissing(['user', 'scheduledClass.classType', 'scheduledClass.instructor']);

            // Return JSON redirect for AJAX callers
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Class booked successfully!',
                    'receipt' => $receipt,
                    'redirect' => route('receipts.show', $receipt->id),
                ]);
            }

            // For standard form submissions, redirect to the receipt view
            return redirect()->route('receipts.show', $receipt->id)
                ->with('success', 'Payment successful! Your class has been booked and receipt is ready.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Receipt creation error: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to process booking. Please try again.',
                ], 500);
            }

            return redirect()->back()->with('error', 'Unable to process booking. Please try again.');
        }
    }

    /**
     * Display the specified receipt.
     */
    public function show(Receipt $receipt)
    {
        // Ensure the user can only view their own receipts
        if ($receipt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        $receipt->loadMissing(['user', 'scheduledClass.classType', 'scheduledClass.instructor']);

        return view('receipts.show', compact('receipt'));
    }

    /**
     * Download receipt as PDF.
     */
    public function download(Receipt $receipt)
    {
        // Ensure the user can only download their own receipts
        if ($receipt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        $receipt->loadMissing(['user', 'scheduledClass.classType', 'scheduledClass.instructor']);

        // Configure Dompdf options
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Initialize Dompdf
        $dompdf = new Dompdf($options);

        // Load the PDF view
        $html = view('receipts.pdf', compact('receipt'))->render();

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (generate)
        $dompdf->render();

        // Download the PDF
        return $dompdf->stream('receipt-' . $receipt->reference_number . '.pdf', ['Attachment' => true]);
    }

    /**
     * Preview receipt in browser without downloading.
     */
    public function preview(Receipt $receipt)
    {
        // Ensure the user can only preview their own receipts
        if ($receipt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        $receipt->loadMissing(['user', 'scheduledClass.classType', 'scheduledClass.instructor']);

        // Configure Dompdf options
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('receipts.pdf', compact('receipt'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream the PDF in browser (inline)
        return $dompdf->stream('receipt-' . $receipt->reference_number . '.pdf', ['Attachment' => false]);
    }

    /**
     * Print receipt.
     */
    public function print(Receipt $receipt)
    {
        // Ensure the user can only print their own receipts
        if ($receipt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        $receipt->loadMissing(['user', 'scheduledClass.classType', 'scheduledClass.instructor']);

        // Use the same show view for printing (print-friendly)
        return view('receipts.show', compact('receipt'));
    }

    /**
     * Get receipt by reference number.
     */
    public function findByReference($reference)
    {
        $receipt = Receipt::where('reference_number', $reference)
            ->with(['user', 'scheduledClass.classType', 'scheduledClass.instructor'])
            ->firstOrFail();

        // Ensure the user can only view their own receipts
        if ($receipt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        return view('receipts.show', compact('receipt'));
    }
}
