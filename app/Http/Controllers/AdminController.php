<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $data = [
            'users' => 120,
            'posts' => 45,
            'settings' => 10,
        ];

        return view('admin.dashboard', compact('data'));
    }
}
