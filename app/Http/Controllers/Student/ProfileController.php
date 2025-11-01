<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Student/Profile', [
            'breadcrumbs' => [
                [
                    'title' => 'Dashboard',
                    'href' => route('dashboard'),
                ],
                [
                    'title' => 'Profile & Settings',
                    'href' => route('student.profile'),
                ],
            ],
        ]);
    }
}
