<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Find user by Google email
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Register new user if not exist
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(16)), // Random password
                    // add other fields as needed
                ]);
            }

            // Update google_id if user exists but doesn't have it set
            if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }

            Auth::login($user);

            return redirect('/home'); // Or wherever you want

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Something went wrong!');
        }
    }
}
