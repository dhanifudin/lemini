<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeedbackController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Student/Feedback', [
            'breadcrumbs' => [
                [
                    'title' => 'Dashboard',
                    'href' => route('dashboard'),
                ],
                [
                    'title' => 'Feedback Center',
                    'href' => route('student.feedback'),
                ],
            ],
        ]);
    }
}
