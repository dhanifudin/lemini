<?php

namespace App\Http\Controllers\Student\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreReflectionController extends Controller
{
    public function __invoke(Request $request, Feedback $feedback): JsonResponse
    {
        $student = $request->user();

        abort_unless($feedback->attempt?->user_id === $student->id, 403);

        $data = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $reflection = $feedback->reflections()->create([
            'user_id' => $student->id,
            'note' => $data['note'],
            'acknowledged_at' => now(),
        ]);

        return response()->json([
            'id' => $reflection->id,
            'note' => $reflection->note,
            'acknowledged_at' => optional($reflection->acknowledged_at)->toIso8601String(),
        ], 201);
    }
}
