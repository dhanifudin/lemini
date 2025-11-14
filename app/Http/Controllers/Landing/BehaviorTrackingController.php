<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\VisitorBehavior;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BehaviorTrackingController extends Controller
{
    public function track(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'required|string|max:50',
            'event_data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $sessionId = $request->session()->getId();
        
        VisitorBehavior::create([
            'session_id' => $sessionId,
            'event_type' => $request->event_type,
            'event_data' => $request->event_data,
            'event_timestamp' => now(),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function analyze(Request $request): JsonResponse
    {
        $sessionId = $request->session()->getId();
        
        $behaviors = VisitorBehavior::where('session_id', $sessionId)
            ->get();

        // Analyze behaviors to predict learning style
        $scores = [
            'visual' => 0,
            'auditory' => 0,
            'kinesthetic' => 0,
        ];

        foreach ($behaviors as $behavior) {
            switch ($behavior->event_type) {
                case 'image_hover':
                    $scores['visual'] += 2;
                    break;
                case 'video_play':
                    $scores['visual'] += 3;
                    $scores['auditory'] += 2;
                    break;
                case 'audio_play':
                    $scores['auditory'] += 5;
                    break;
                case 'interaction':
                    $scores['kinesthetic'] += 3;
                    break;
                case 'button_click':
                    $scores['kinesthetic'] += 1;
                    break;
            }
        }

        $total = array_sum($scores);
        
        if ($total < 5) {
            return response()->json([
                'prediction' => null,
                'message' => 'Not enough data to predict learning style',
            ]);
        }

        arsort($scores);
        $predictedStyle = array_key_first($scores);
        $confidence = $total > 0 ? $scores[$predictedStyle] / $total : 0;

        return response()->json([
            'prediction' => $predictedStyle,
            'confidence' => round($confidence, 2),
            'scores' => $scores,
        ]);
    }
}
