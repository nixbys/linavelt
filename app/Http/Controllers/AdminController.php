<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Fetch admin data dynamically (example data for now)
        $data = [
            'users' => 120,
            'posts' => 45,
            'settings' => 10,
        ];

        return view('admin.dashboard', compact('data'));
    }
}
