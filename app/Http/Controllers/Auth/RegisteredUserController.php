<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->has('whatsapp')) {
            $number = preg_replace('/\D/', '', $request->whatsapp);
            if (str_starts_with($number, '0')) {
                $normalized = '62' . substr($number, 1);
            } elseif (!str_starts_with($number, '62')) {
                $normalized = '62' . $number;
            } else {
                $normalized = $number;
            }
            $request->merge(['whatsapp' => $normalized]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:20', 'unique:users,whatsapp'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'whatsapp' => $request->whatsapp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'WALI_SANTRI',
        ]);

        Guardian::create([
            'user_id' => $user->id,
            'full_name' => $request->name,
            'whatsapp' => $request->whatsapp,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
