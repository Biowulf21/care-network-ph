<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('superadmin')) {
            return redirect()->route('dashboard.superadmin');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('dashboard.admin');
        }

        // default: show simple dashboard view
        return view('dashboard');
    }

    public function superadmin()
    {
        return view('dashboard.superadmin');
    }

    public function admin()
    {
        return view('dashboard.admin');
    }
}
