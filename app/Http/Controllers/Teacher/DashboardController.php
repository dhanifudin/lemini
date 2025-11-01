<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Teacher/Dashboard', [
            'breadcrumbs' => [
                [
                    'title' => 'Teacher Dashboard',
                    'href' => route('teacher.dashboard'),
                ],
            ],
        ]);
    }
}
