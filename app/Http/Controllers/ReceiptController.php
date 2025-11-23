<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Receipt;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'scheduled_class_id' => 'required|integer|exists:scheduled_classes,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        // create receipt record
        $receipt = Receipt::create([
            'user_id' => $user->id,
            'scheduled_class_id' => $data['scheduled_class_id'],
            'payment_method' => $data['payment_method'],
            'amount' => $data['amount'],
            'reference_number' => strtoupper(Str::random(10)),
        ]);

        // Add booking for upcoming classes (if not already booked)
        // Booking::firstOrCreate([
        //     'user_id' => $user->id,
        //     'scheduled_class_id' => $data['scheduled_class_id'],
        // ]);

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
            ->with('success', 'Payment successful! Your receipt is ready.');
    }

    public function show(Receipt $receipt)
    {
        $receipt->loadMissing(['user', 'scheduledClass.classType']);
        return view('member.payment_success', compact('receipt'));
    }
}
