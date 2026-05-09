<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\ClientProfile;
use App\Models\Account;       

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        // Use a transaction to ensure all three records are created together
        $user = DB::transaction(function () use ($request) {
            
            // 1. Create the User as 'Client'
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Client', 
            ]);

            // 2. Create the Client Profile
            // Note: Using $request->name for first_name to match your provided logic
            $profile = ClientProfile::create([
                'user_id' => $user->id,
                'first_name' => $request->name,
                'last_name' => '', 
                'phone_number' => 'N/A',
            ]);

            // 3. Create a Default Savings Account
            // Using $profile->id ensures the account is linked to the Profile ID
            Account::create([
                'client_id' => $profile->id, 
                'account_type' => 'Primary Savings',
                'balance' => 0.00,
                'currency' => 'USD',
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
