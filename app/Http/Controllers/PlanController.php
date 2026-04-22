<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\MemberSubscription;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PlanController extends Controller
{
    /**
     * Display all active plans
     */
    public function index()
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('price', 'asc')
            ->get();

        $currentSubscription = null;
        if (Auth::check()) {
            $currentSubscription = MemberSubscription::where('member_id', Auth::id())
                ->where('status', 'active')
                ->where('end_date', '>=', Carbon::now())
                ->first();
        }

        return view('plans.index', compact('plans', 'currentSubscription'));
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request, Plan $plan)
    {
        $request->validate([
            'payment_method' => 'required|string|in:credit_card,mobile_money,bank_transfer'
        ]);

        $user = Auth::user();

        // Check existing active subscription
        $existingSubscription = MemberSubscription::where('member_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::now())
            ->first();

        if ($existingSubscription) {
            return redirect()->route('plans.index')
                ->with('error', 'You already have an active subscription. Please cancel it first.');
        }

        // Create new subscription
        $subscription = MemberSubscription::create([
            'member_id' => $user->id,
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays($plan->duration_days),
            'price' => $plan->price,
            'currency' => $plan->currency,
            'status' => 'active',
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending'
        ]);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'subscription',
            'title' => 'Subscription Activated',
            'message' => 'You have successfully subscribed to the ' . $plan->name . ' plan.',
            'data' => json_encode(['subscription_id' => $subscription->id]),
            'read' => false
        ]);

        return redirect()->route('plans.index')
            ->with('success', 'Successfully subscribed to ' . $plan->name . ' plan!');
    }

    /**
     * Cancel current subscription
     */
    public function cancelSubscription(Request $request)
    {
        $user = Auth::user();

        $subscription = MemberSubscription::where('member_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::now())
            ->first();

        if (!$subscription) {
            return redirect()->route('plans.index')
                ->with('error', 'No active subscription found.');
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now()
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'subscription',
            'title' => 'Subscription Cancelled',
            'message' => 'Your subscription has been cancelled. You will have access until ' . $subscription->end_date->format('M d, Y'),
            'data' => json_encode(['subscription_id' => $subscription->id]),
            'read' => false
        ]);

        return redirect()->route('plans.index')
            ->with('success', 'Your subscription has been cancelled.');
    }
}
