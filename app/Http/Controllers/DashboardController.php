<?php

namespace App\Http\Controllers;

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

        if ($user->hasRole('delegate')) {
            return redirect()->route('dashboard.delegate');
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

    public function delegate()
    {
        return view('dashboard.delegate');
    }
}
