<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PracticeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Student/Practice', [
            'breadcrumbs' => [
                [
                    'title' => 'Dashboard',
                    'href' => route('dashboard'),
                ],
                [
                    'title' => 'Practice & Resources',
                    'href' => route('student.practice'),
                ],
            ],
        ]);
    }
}
