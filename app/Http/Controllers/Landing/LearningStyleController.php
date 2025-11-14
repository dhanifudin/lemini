<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\LearningStylePreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LearningStyleController extends Controller
{
    public function save(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'learning_style' => 'required|in:visual,auditory,kinesthetic',
            'detection_method' => 'required|in:quiz,explicit,behavioral,default',
            'quiz_results' => 'nullable|array',
            'confidence_score' => 'nullable|numeric|min:0|max:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $sessionId = $request->session()->getId();
        
        $preference = LearningStylePreference::updateOrCreate(
            [
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
            ],
            [
                'learning_style' => $request->learning_style,
                'detection_method' => $request->detection_method,
                'quiz_results' => $request->quiz_results,
                'confidence_score' => $request->confidence_score,
            ]
        );

        return response()->json([
            'success' => true,
            'preference' => $preference,
        ]);
    }

    public function get(Request $request): JsonResponse
    {
        $sessionId = $request->session()->getId();
        
        $preference = LearningStylePreference::where('session_id', $sessionId)
            ->orWhere('user_id', auth()->id())
            ->latest()
            ->first();

        return response()->json([
            'preference' => $preference,
        ]);
    }
}
