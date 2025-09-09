<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            
        ]);
    }

    /**
     * Update the user's profile information.
     */

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        // dd($request->all());

        $user = $request->user();

        $data = $request->validated();

        // pfp
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo'] = $path;
        }

        //email
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        //student and teacher stuff

        if ($user->role === 'student') {
            if ($user->student) {
                $user->student->update([
                    'grade' => $request->input('grade'),
                ]);
            } else {
                \App\Models\Student::create([
                    'user_id' => $user->id,
                    'grade' => $request->input('grade'),
                ]);
            }
        }
        
        if ($user->role === 'teacher') {
            if ($user->teacher) {
                $user->teacher->update([
                    'education' => $request->input('education'),
                    'bio' => $request->input('bio'),
                ]);
            } else {
                \App\Models\Teacher::create([
                    'user_id' => $user->id,
                    'education' => $request->input('education'),
                    'bio' => $request->input('bio'),
                ]);
            }
        }


        
        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
