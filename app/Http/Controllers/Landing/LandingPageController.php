<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\LearningStylePreference;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LandingPageController extends Controller
{
    public function index(Request $request): Response
    {
        $sessionId = $request->session()->getId();
        
        // Get or create learning style preference
        $preference = LearningStylePreference::where('session_id', $sessionId)
            ->orWhere('user_id', auth()->id())
            ->latest()
            ->first();

        $learningStyle = $preference?->learning_style ?? 'visual';
        $detectionMethod = $preference?->detection_method ?? 'default';

        return Inertia::render('Landing/Index', [
            'learningStyle' => $learningStyle,
            'detectionMethod' => $detectionMethod,
            'sessionId' => $sessionId,
        ]);
    }
}
