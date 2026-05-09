<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->role === 'Staff') {
            return redirect()->route('staff.dashboard');
        }

        if ($user->role === 'Client') {
            return redirect()->route('client.dashboard');
        }

        return view('admin.dashboard');
    }
}
