<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

/**
 * Controller for handling user registration and email verification.
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Store a new user and send a verification email.
     *
     * This method validates the user input, creates a new user in the database,
     * generates a temporary email verification link, sends the email with the
     * verification link, and then redirects the user to the email notification view.
     *
     * @param \Illuminate\Http\Request $request The data sent in the request.
     * @return \Illuminate\Http\RedirectResponse Redirects the user to the email verification notice view.
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails.
     */
    public function store(Request $request) : RedirectResponse
    {
        $rules = [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'g-recaptcha-response' => 'required|captcha'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate the email verification link with a temporary signature
        $verificationUrl = URL::temporarySignedRoute(
            'verify.email', // Route the user will be redirected to
            now()->addMinutes(60), // Expiration time for the link
            ['id' => $user->id] // Necessary data for verification
        );

        // Send the verification email
        Mail::to($user->email)->send(new VerifyEmail($verificationUrl));

        // Update the user's verification link sent timestamp
        $user->verification_link_sent_at = now();
        $user->save();

        // Set an internal redirect session variable
        session(['internal_redirect' => true]);
        return redirect()->route('email.notice');
    }

    /**
     * Resend the email verification link.
     *
     * This method validates the user input, checks if the user exists, and if the email
     * is already verified. If the email is not verified, it checks if a verification email
     * has already been sent within the last hour. If not, it generates a new verification
     * link, sends the email, and redirects the user with a success message.
     *
     * @param \Illuminate\Http\Request $request The data sent in the request.
     * @return \Illuminate\Http\RedirectResponse Redirects the user to the signin page with a success or error message.
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails.
     */
    public function resendVerificationEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'g-recaptcha-response' => 'required|captcha',
        ],
        [
            'g-recaptcha-response.required' => 'Captcha is required',
            'g-recaptcha-response.captcha' => 'Captcha is invalid',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->email_verified) {
            return redirect()->route('auth.signin')->with('error_message', 'Your email is already verified or user not found.');
        }

        // Check if a verification email has already been sent within the last hour
        $linkExpiration = $user->verification_link_sent_at
            ? Carbon::parse($user->verification_link_sent_at)->addMinutes(60)
            : null;

        if($linkExpiration && now()->lessThan($linkExpiration)) {
            return redirect()->route('auth.signin')->with('error_message', 'A verification email has already been sent. Please check your inbox.');
        }

        $verificationUrl = URL::temporarySignedRoute(
            'verify.email',
            now()->addMinutes(60),
            ['id' => $user->id]
        );

        Mail::to($user->email)->send(new VerifyEmail($verificationUrl));

        return redirect()->route('auth.signin')->with('message', 'A new verification email has been sent!');
    }

    /**
     * Verify the user's email address.
     *
     * This method validates the verification link's signature. If valid, it marks the
     * user as verified. If the link is invalid or the user is already verified, it
     * redirects the user with an appropriate message.
     *
     * @param \Illuminate\Http\Request $request The request data, including the user ID and verification link.
     * @return \Illuminate\Http\RedirectResponse Redirects the user to the signin page with either a success or error message.
     */
    public function verifyEmail(Request $request) : RedirectResponse
    {
        // Check if the verification link is valid
        if (!$request->hasValidSignature()) {
            return redirect()->route('auth.signin')->with('error_message', 'Invalid verification link');
        }

        $user = User::find($request->id);

        if (!$user) {
            return redirect()->route('auth.signin')->with('error_message', 'User not found');
        }

        // If the email is already verified, notify the user
        if ($user->email_verified_at) {
            return redirect()->route('auth.signin')->with('message', 'Your email is already verified.');
        }

        $user->email_verified_at = now();
        $user->email_verified = true;
        $user->save();

        return redirect()->route('auth.signin')->with('message', 'Your email has been successfully verified!');
    }
}
