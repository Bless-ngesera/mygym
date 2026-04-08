<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    /**
     * Handle AI chat requests
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $message = strtolower(trim($request->message));
        $user = Auth::user();

        // Get personalized response based on user data
        $reply = $this->getPersonalizedResponse($message, $user);

        return response()->json([
            'success' => true,
            'reply' => $reply
        ]);
    }

    /**
     * Get personalized AI response
     */
    private function getPersonalizedResponse($message, $user)
    {
        // Workout related queries
        if (str_contains($message, 'workout') || str_contains($message, 'exercise') || str_contains($message, 'gym')) {
            if (str_contains($message, 'beginner')) {
                return "🏋️ For beginners, I recommend starting with:\n\n• 3x/week full-body workouts\n• Focus on compound exercises (squats, pushups, rows)\n• 20-30 minute sessions\n• Rest days between workouts\n\nWould you like a sample beginner routine, " . ($user->name ?? 'friend') . "?";
            } elseif (str_contains($message, 'chest') || str_contains($message, 'push')) {
                return "💪 Great chest workout:\n\n• Bench Press: 4x8-10\n• Incline Dumbbell: 3x10-12\n• Push-ups: 3x15\n• Chest Flyes: 3x12\n\nRemember to warm up properly!";
            } elseif (str_contains($message, 'leg') || str_contains($message, 'squat')) {
                return "🦵 Leg day essentials:\n\n• Squats: 4x8-10\n• Lunges: 3x12 each leg\n• Leg Press: 3x10-12\n• Calf Raises: 4x15\n\nDon't skip leg day! 💪";
            } elseif (str_contains($message, 'back')) {
                return "🔥 Back workout routine:\n\n• Pull-ups/Lat Pulldowns: 4x8-10\n• Barbell Rows: 3x10-12\n• Seated Cable Rows: 3x12\n• Deadlifts: 3x5\n\nBuild that V-taper!";
            } elseif (str_contains($message, 'shoulder')) {
                return "💪 Shoulder workout:\n\n• Overhead Press: 4x8-10\n• Lateral Raises: 3x12-15\n• Front Raises: 3x12\n• Face Pulls: 3x15\n\nBuild those boulder shoulders!";
            } elseif (str_contains($message, 'arms') || str_contains($message, 'bicep') || str_contains($message, 'tricep')) {
                return "💪 Arm day workout:\n\nBiceps:\n• Barbell Curls: 4x8-10\n• Dumbbell Curls: 3x10-12\n• Hammer Curls: 3x10\n\nTriceps:\n• Tricep Pushdowns: 4x10-12\n• Skull Crushers: 3x8-10\n• Dips: 3x12\n\nGet those guns! 💪";
            } elseif (str_contains($message, 'abs') || str_contains($message, 'core')) {
                return "🔥 Core strengthening:\n\n• Planks: 3x30-60 sec\n• Russian Twists: 3x15 each side\n• Leg Raises: 3x12\n• Bicycle Crunches: 3x20\n• Mountain Climbers: 3x30 sec\n\nConsistency is key for visible abs!";
            } else {
                return "💪 Here's a balanced weekly workout plan:\n\nMonday: Chest & Triceps\nTuesday: Back & Biceps\nWednesday: Legs & Core\nThursday: Shoulders & Cardio\nFriday: Full Body HIIT\nSaturday: Active Recovery\nSunday: Rest\n\nWant specific exercises for any day? Just ask!";
            }
        }

        // Nutrition related queries
        elseif (str_contains($message, 'nutrition') || str_contains($message, 'diet') || str_contains($message, 'food') || str_contains($message, 'meal') || str_contains($message, 'eat')) {
            if (str_contains($message, 'breakfast')) {
                return "🥣 Healthy breakfast ideas:\n\n• Greek yogurt with berries & granola\n• Oatmeal with banana & nuts\n• Protein smoothie with spinach\n• Eggs with avocado toast\n\nAim for 20-30g protein to start your day!";
            } elseif (str_contains($message, 'lunch')) {
                return "🥗 Nutritious lunch options:\n\n• Grilled chicken quinoa bowl\n• Tuna salad wrap\n• Lentil soup with whole grain bread\n• Salmon with roasted vegetables\n\nBalance protein, complex carbs, and veggies!";
            } elseif (str_contains($message, 'dinner')) {
                return "🍽️ Healthy dinner ideas:\n\n• Lean steak with sweet potato\n• Baked fish with brown rice\n• Turkey meatballs with zucchini noodles\n• Stir-fry tofu with vegetables\n\nEat 2-3 hours before bedtime!";
            } elseif (str_contains($message, 'snack')) {
                return "🍎 Smart snack choices:\n\n• Apple with peanut butter\n• Protein shake\n• Handful of almonds\n• Cottage cheese with berries\n• Hard-boiled eggs\n\nKeep snacks under 200 calories!";
            } elseif (str_contains($message, 'protein')) {
                return "🥩 Best protein sources:\n\n• Chicken breast (31g/100g)\n• Fish (20-25g/100g)\n• Eggs (6g each)\n• Greek yogurt (10g/100g)\n• Lentils (9g/100g)\n• Whey protein (20-25g/scoop)\n\nAim for 1.6-2.2g protein per kg body weight!";
            } elseif (str_contains($message, 'vegan') || str_contains($message, 'vegetarian')) {
                return "🌱 Plant-based protein sources:\n\n• Lentils & beans\n• Tofu & tempeh\n• Quinoa & chickpeas\n• Seitan & edamame\n• Nuts & seeds\n• Plant-based protein powder\n\nCombine different sources for complete protein!";
            } else {
                return "🥗 General nutrition tips:\n\n• Eat protein with every meal\n• Include colorful vegetables\n• Stay hydrated (2-3L water daily)\n• Limit processed foods\n• Don't skip meals\n• Track calories if weight is a goal\n\nWant specific meal plans or recipes? Let me know your goals!";
            }
        }

        // Motivation related queries
        elseif (str_contains($message, 'motivation') || str_contains($message, 'motivate') || str_contains($message, 'tired') || str_contains($message, 'give up') || str_contains($message, 'discouraged')) {
            $motivationalQuotes = [
                "🔥 Remember why you started! Every workout is progress, no matter how small.",
                "💪 You're stronger than you think. The pain you feel today will be the strength you feel tomorrow.",
                "🌟 Success isn't always about greatness. It's about consistency. Consistent hard work leads to success.",
                "🏆 Your body can stand almost anything. It's your mind that you have to convince.",
                "⚡ The only bad workout is the one that didn't happen. You've already won by showing up!"
            ];

            $quote = $motivationalQuotes[array_rand($motivationalQuotes)];

            return $quote . "\n\nYou've got this, " . ($user->name ?? 'champion') . "! 💪 What specific goal are you working toward right now?";
        }

        // Progress related queries
        elseif (str_contains($message, 'progress') || str_contains($message, 'track') || str_contains($message, 'measure') || str_contains($message, 'results')) {
            return "📊 Track your progress effectively:\n\n• Take photos every 4 weeks\n• Measure weight weekly (same time/day)\n• Track workout weights/reps\n• Measure body parts monthly\n• Note how clothes fit\n• Track energy & mood levels\n• Celebrate non-scale victories!\n\nConsistency > Intensity! Want me to help you set up a tracking system?";
        }

        // Cardio related
        elseif (str_contains($message, 'cardio') || str_contains($message, 'running') || str_contains($message, 'walk') || str_contains($message, 'jog')) {
            return "🏃 Cardio recommendations:\n\n• Beginners: 20 min walking/jogging\n• Intermediate: HIIT 15-20 min\n• Advanced: 45 min running\n• Low impact: Swimming/cycling\n• Fat burning: Fasted morning walk\n\nAim for 150 min moderate or 75 min intense cardio weekly!\n\nWhat's your current fitness level?";
        }

        // Recovery related
        elseif (str_contains($message, 'recovery') || str_contains($message, 'rest') || str_contains($message, 'sleep') || str_contains($message, 'sore')) {
            return "😴 Recovery is crucial! Tips:\n\n• Get 7-9 hours quality sleep\n• Take rest days seriously\n• Stretch dynamically before, statically after\n• Foam roll sore muscles\n• Stay hydrated (add electrolytes)\n• Eat protein within 30 min post-workout\n• Try active recovery (light walking)\n\nYour muscles grow during rest, not during workouts!";
        }

        // Weight loss related
        elseif (str_contains($message, 'weight loss') || str_contains($message, 'fat loss') || str_contains($message, 'lose weight')) {
            return "🎯 For effective weight loss:\n\n• Calorie deficit (500-750 calories/day)\n• High protein intake (preserve muscle)\n• Strength training 3-4x/week\n• NEAT (non-exercise activity)\n• 8-10k steps daily\n• Sleep & stress management\n• Patience (1-2 lbs/week is healthy)\n\nWant a sample meal plan or workout routine for weight loss?";
        }

        // Muscle building related
        elseif (str_contains($message, 'muscle') || str_contains($message, 'gain weight') || str_contains($message, 'bulk')) {
            return "💪 For muscle building:\n\n• Calorie surplus (+300-500 calories)\n• 1.6-2.2g protein per kg body weight\n• Progressive overload in training\n• Compound lifts (squat, deadlift, bench)\n• 6-12 rep range for hypertrophy\n• 7-9 hours sleep for recovery\n• Consistency over perfection\n\nWhat's your current training experience?";
        }

        // Greetings
        elseif (str_contains($message, 'hi') || str_contains($message, 'hello') || str_contains($message, 'hey') || str_contains($message, 'good morning') || str_contains($message, 'good evening')) {
            $time = date('H');
            $greeting = $time < 12 ? 'Good morning' : ($time < 18 ? 'Good afternoon' : 'Good evening');

            return "👋 {$greeting}, " . ($user->name ?? 'friend') . "! I'm your AI fitness coach. \n\nHow can I help you today?\n\nAsk me about:\n• 💪 Workouts & exercises\n• 🥗 Nutrition & meal ideas\n• 🔥 Motivation & mindset\n• 📊 Progress tracking\n• 🏃 Cardio & recovery\n\nWhat's your fitness goal today?";
        }

        // Help
        elseif (str_contains($message, 'help') || str_contains($message, 'what can you do') || str_contains($message, 'commands')) {
            return "🤖 I can help you with:\n\n💪 WORKOUTS\n• \"Workout for chest\"\n• \"Beginner leg workout\"\n• \"Arm day routine\"\n\n🥗 NUTRITION\n• \"Healthy breakfast ideas\"\n• \"Best protein sources\"\n• \"Meal prep tips\"\n\n🔥 MOTIVATION\n• \"Need motivation\"\n• \"Feeling tired\"\n• \"Stay consistent\"\n\n📊 PROGRESS\n• \"How to track progress\"\n• \"Measure results\"\n\n🏃 CARDIO & RECOVERY\n• \"Cardio routine\"\n• \"Recovery tips\"\n\nWhat would you like to know?";
        }

        // Default response
        else {
            return "I'm here to help with your fitness journey, " . ($user->name ?? 'friend') . "! 💪\n\nYou can ask me about:\n• \"Workout for beginners\"\n• \"Healthy meal ideas\" \n• \"How to stay motivated\"\n• \"Track my progress\"\n• \"Cardio routine\"\n• \"Recovery tips\"\n\nWhat specific fitness goal can I help you with today?";
        }
    }
}
