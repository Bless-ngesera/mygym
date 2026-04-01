<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Receipt;
use App\Models\ScheduledClass;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
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
            return redirect()->back()->withErrors(['error' => 'Only members can book classes.']);
        }

        // Get the scheduled class
        $scheduledClass = ScheduledClass::findOrFail($data['scheduled_class_id']);

        // Check if the class is in the future
        if ($scheduledClass->date_time->isPast()) {
            return redirect()->back()->withErrors(['error' => 'Cannot book past classes.']);
        }

        // Check if already booked
        $existingBooking = $user->bookings()->where('scheduled_class_id', $scheduledClass->id)->exists();
        if ($existingBooking) {
            return redirect()->back()->withErrors(['error' => 'You already booked this class.']);
        }

        // Create receipt record
        $receipt = Receipt::create([
            'user_id' => $user->id,
            'scheduled_class_id' => $data['scheduled_class_id'],
            'payment_method' => $data['payment_method'],
            'amount' => $data['amount'],
            'reference_number' => strtoupper(Str::random(10)),
            'payment_contact' => $data['payment_contact'] ?? null,
        ]);

        // Create booking for the class (THIS IS THE KEY LINE)
        $user->bookings()->attach($scheduledClass->id);

        // Ensure relations are loaded for the view
        $receipt->loadMissing(['user', 'scheduledClass.classType']);

        // Return JSON redirect for AJAX callers
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('receipts.show', $receipt->id),
            ]);
        }

        // For standard form submissions, redirect to the receipt view
        return redirect()->route('receipts.show', $receipt->id)
            ->with('success', 'Payment successful! Your class has been booked and receipt is ready.');
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
        return view('member.payment_success', compact('receipt'));
    }
}
