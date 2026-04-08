<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    /**
     * Chat with AI coach
     */
    public function chat(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000'
            ]);

            $message = $request->message;
            $response = $this->getAIResponse($message);

            return response()->json([
                'success' => true,
                'response' => $response
            ]);
        } catch (\Exception $e) {
            Log::error('AI Chat error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Unable to process your request. Please try again.'
            ], 500);
        }
    }

    /**
     * Get suggested questions for users
     */
    public function getSuggestions()
    {
        try {
            $suggestions = [
                "How can I improve my workout routine?",
                "What should I eat before a workout?",
                "Tips for staying motivated",
                "Best exercises for weight loss",
                "How to build muscle effectively?",
                "What's a good warm-up routine?",
                "How much water should I drink daily?",
                "Benefits of strength training",
                "How to prevent workout injuries?",
                "Best time to exercise for weight loss"
            ];

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);
        } catch (\Exception $e) {
            Log::error('Get suggestions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Unable to load suggestions'
            ], 500);
        }
    }

    /**
     * Generate personalized workout plan
     */
    public function generateWorkoutPlan(Request $request)
    {
        try {
            $request->validate([
                'goal' => 'required|string|in:weight_loss,muscle_gain,endurance,general_fitness',
                'days_per_week' => 'required|integer|min:1|max:7'
            ]);

            $plan = $this->getWorkoutPlan($request->goal, $request->days_per_week);

            return response()->json([
                'success' => true,
                'plan' => $plan
            ]);
        } catch (\Exception $e) {
            Log::error('Generate workout plan error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Unable to generate workout plan'
            ], 500);
        }
    }

    /**
     * Get nutrition advice based on goal
     */
    public function getNutritionAdvice(Request $request)
    {
        try {
            $request->validate([
                'goal' => 'required|string|in:weight_loss,muscle_gain,maintenance'
            ]);

            $advice = $this->getNutritionAdviceByGoal($request->goal);

            return response()->json([
                'success' => true,
                'advice' => $advice
            ]);
        } catch (\Exception $e) {
            Log::error('Get nutrition advice error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Unable to get nutrition advice'
            ], 500);
        }
    }

    /**
     * Get AI response based on user message
     */
    private function getAIResponse($message)
    {
        $msg = strtolower($message);

        if (str_contains($msg, 'workout') || str_contains($msg, 'exercise')) {
            return "💪 Great question! For optimal results, I recommend a mix of cardio and strength training. Try 3-4 strength sessions and 2-3 cardio sessions per week. Would you like a personalized workout plan?";
        }

        if (str_contains($msg, 'nutrition') || str_contains($msg, 'diet') || str_contains($msg, 'food') || str_contains($msg, 'eat')) {
            return "🥗 Nutrition is key! Focus on whole foods: lean proteins, complex carbs, healthy fats, and plenty of vegetables. Aim for 1.6-2.2g of protein per kg of body weight. What specific nutrition advice are you looking for?";
        }

        if (str_contains($msg, 'motivation') || str_contains($msg, 'motivate') || str_contains($msg, 'discouraged')) {
            return "🔥 Remember why you started! Every workout brings you one step closer to your goals. Set small achievable targets and celebrate every win. You've got this! 💪";
        }

        if (str_contains($msg, 'protein')) {
            return "💪 Excellent protein sources include chicken, fish, eggs, Greek yogurt, lentils, beans, and tofu. Aim for 20-40g per meal distributed throughout the day for optimal muscle synthesis.";
        }

        if (str_contains($msg, 'weight loss') || str_contains($msg, 'lose weight')) {
            return "🏋️ For weight loss, combine a calorie deficit with strength training to preserve muscle mass. Aim for 500-700 calorie deficit per day for sustainable loss of 0.5-1kg per week. Cardio helps, but don't skip the weights!";
        }

        if (str_contains($msg, 'muscle') || str_contains($msg, 'gain muscle') || str_contains($msg, 'build muscle')) {
            return "💪 To build muscle, focus on progressive overload - gradually increase weight or reps over time. Eat in a slight calorie surplus with adequate protein (1.6-2.2g/kg body weight). Rest and recovery are just as important as training!";
        }

        if (str_contains($msg, 'cardio')) {
            return "🏃 Cardio improves heart health and burns calories. Aim for 150 minutes of moderate or 75 minutes of vigorous cardio weekly. Try HIIT (High-Intensity Interval Training) for maximum efficiency in less time!";
        }

        if (str_contains($msg, 'stretch') || str_contains($msg, 'flexibility') || str_contains($msg, 'mobility')) {
            return "🧘 Stretching improves flexibility and reduces injury risk. Hold each stretch for 15-30 seconds, never bounce. Try dynamic stretches before workouts and static stretches after. Yoga is excellent for overall flexibility!";
        }

        if (str_contains($msg, 'sleep') || str_contains($msg, 'rest')) {
            return "😴 Sleep is crucial for recovery! Aim for 7-9 hours of quality sleep per night. Proper rest improves performance, reduces injury risk, and helps with weight management. Create a consistent sleep schedule.";
        }

        if (str_contains($msg, 'water') || str_contains($msg, 'hydration')) {
            return "💧 Stay hydrated! Drink 2-3 liters of water daily, more if you exercise heavily. Water regulates body temperature, lubricates joints, and transports nutrients. Drink before, during, and after workouts.";
        }

        if (str_contains($msg, 'injury') || str_contains($msg, 'hurt') || str_contains($msg, 'pain')) {
            return "⚠️ Listen to your body! If you're in pain, stop exercising and rest. Apply RICE (Rest, Ice, Compression, Elevation) for acute injuries. Always warm up properly and use correct form to prevent injuries. Consult a doctor for persistent pain.";
        }

        if (str_contains($msg, 'beginner') || str_contains($msg, 'start')) {
            return "🌟 Welcome to your fitness journey! Start slow with 2-3 workouts per week. Focus on learning proper form before increasing intensity. Begin with full body workouts and gradually increase frequency. Consistency is more important than intensity!";
        }

        if (str_contains($msg, 'meal') || str_contains($msg, 'recipe')) {
            return "🍽️ Healthy eating doesn't have to be boring! Try: Greek yogurt with berries, grilled chicken with quinoa and vegetables, salmon with sweet potato, or plant-based protein bowls. Meal prep on Sundays to stay on track!";
        }

        return "🤖 I'm your AI fitness coach! I can help with:\n\n• 💪 Workout plans & exercises\n• 🥗 Nutrition advice & meal ideas\n• 🔥 Motivation & goal setting\n• ⚖️ Weight loss or muscle building tips\n• 🧘 Recovery & injury prevention\n• 😴 Sleep & hydration guidance\n\nWhat would you like to know about your fitness journey today?";
    }

    /**
     * Get workout plan based on goal and days per week
     */
    private function getWorkoutPlan($goal, $daysPerWeek)
    {
        $plans = [
            'weight_loss' => [
                'focus' => 'Calorie burning and metabolism boost',
                'workouts' => [
                    'Day 1' => 'Full body HIIT + 20 min cardio',
                    'Day 2' => 'Upper body strength + 30 min cardio',
                    'Day 3' => 'Lower body strength + 20 min cardio',
                    'Day 4' => 'Active recovery (walking, light cardio)',
                    'Day 5' => 'Full body circuit training',
                    'Day 6' => 'Cardio focus (run, swim, cycle)',
                    'Day 7' => 'Rest and recovery'
                ]
            ],
            'muscle_gain' => [
                'focus' => 'Progressive overload and strength building',
                'workouts' => [
                    'Day 1' => 'Chest and Triceps',
                    'Day 2' => 'Back and Biceps',
                    'Day 3' => 'Legs and Shoulders',
                    'Day 4' => 'Rest',
                    'Day 5' => 'Push Day (Chest, Shoulders, Triceps)',
                    'Day 6' => 'Pull Day (Back, Biceps)',
                    'Day 7' => 'Rest'
                ]
            ],
            'endurance' => [
                'focus' => 'Building stamina and cardiovascular health',
                'workouts' => [
                    'Day 1' => 'Long slow run (45-60 min)',
                    'Day 2' => 'Strength training + 20 min cardio',
                    'Day 3' => 'Interval training (HIIT)',
                    'Day 4' => 'Cross-training (swim/cycle)',
                    'Day 5' => 'Tempo run (30-40 min)',
                    'Day 6' => 'Strength training',
                    'Day 7' => 'Active recovery'
                ]
            ],
            'general_fitness' => [
                'focus' => 'Balanced approach for overall health',
                'workouts' => [
                    'Day 1' => 'Full body strength',
                    'Day 2' => 'Cardio (30 min)',
                    'Day 3' => 'Upper body + core',
                    'Day 4' => 'Active recovery (yoga/stretching)',
                    'Day 5' => 'Lower body + core',
                    'Day 6' => 'HIIT or cardio',
                    'Day 7' => 'Rest'
                ]
            ]
        ];

        $plan = $plans[$goal] ?? $plans['general_fitness'];

        $customizedWorkouts = [];
        for ($i = 1; $i <= $daysPerWeek; $i++) {
            $dayKey = 'Day ' . $i;
            if (isset($plan['workouts'][$dayKey])) {
                $customizedWorkouts[$dayKey] = $plan['workouts'][$dayKey];
            }
        }

        $plan['workouts'] = $customizedWorkouts;
        $plan['days_per_week'] = $daysPerWeek;
        $plan['recommendation'] = "For $daysPerWeek days per week, focus on compound exercises and consistency over intensity. Rest days are crucial for recovery!";

        return $plan;
    }

    /**
     * Get nutrition advice based on goal
     */
    private function getNutritionAdviceByGoal($goal)
    {
        $advice = [
            'weight_loss' => [
                'calorie_deficit' => '500-700 calories per day',
                'protein' => '1.6-2.2g per kg of body weight',
                'tips' => [
                    'Eat more vegetables and lean proteins',
                    'Stay hydrated (2-3 liters water daily)',
                    'Avoid liquid calories (soda, juice, alcohol)',
                    'Practice portion control',
                    'Don\'t skip meals - eat every 3-4 hours',
                    'Limit processed foods and added sugars',
                    'Eat protein with every meal for satiety'
                ]
            ],
            'muscle_gain' => [
                'calorie_surplus' => '300-500 calories above maintenance',
                'protein' => '1.6-2.2g per kg of body weight',
                'tips' => [
                    'Eat protein with every meal (20-40g)',
                    'Complex carbs for energy (oats, rice, potatoes)',
                    'Healthy fats for hormones (avocado, nuts, olive oil)',
                    'Eat every 3-4 hours',
                    'Post-workout nutrition within 2 hours',
                    'Don\'t fear carbs - they fuel your workouts',
                    'Consider protein shakes for convenience'
                ]
            ],
            'maintenance' => [
                'calories' => 'Maintenance level',
                'protein' => '1.2-1.6g per kg of body weight',
                'tips' => [
                    'Balanced meals with all macronutrients',
                    'Listen to hunger cues',
                    'Eat whole foods 80% of the time',
                    'Stay consistent with meal timing',
                    'Allow flexible eating (80/20 rule)',
                    'Monitor portion sizes',
                    'Adjust based on activity level'
                ]
            ]
        ];

        return $advice[$goal] ?? $advice['maintenance'];
    }
}
