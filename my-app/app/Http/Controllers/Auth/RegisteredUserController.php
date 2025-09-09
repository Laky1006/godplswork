<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Student;
use App\Models\Teacher;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {

        //dd($request->all());
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-zĀ-žā-ž\s]+$/u'],
            'username' => ['required', 'string', 'min:3', 'max:20', 'regex:/^[A-Za-z0-9_.]+$/', 'unique:users,username'],
            'email' => ['required', 'string', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()  // at least one uppercase and one lowercase letter
                    ->numbers()    // at least one number
                    ->symbols(),   // at least one special character
            ],
            'role' => ['required', 'in:student,teacher'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        event(new Registered($user));

        if ($request->role === 'student') {
            Student::create(['user_id' => $user->id]);
        } elseif ($request->role === 'teacher') {
            Teacher::create(['user_id' => $user->id]);
        }

        Auth::login($user);

        // return redirect(route('dashboard', absolute: false));
        return redirect('/profile');
    }
}

