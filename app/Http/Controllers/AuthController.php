<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Crypt;

/**
 * Controller for handling user authentication, Google 2FA setup, and logout.
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Validate the user's data for authentication.
     *
     * This method validates the user's email and checks if the user exists in the database.
     * It also verifies if the user's email is verified and if Google 2FA is set up.
     * If Google 2FA is not set up, it triggers the 2FA setup process.
     * If everything is validated, it stores the email in the session and redirects to TOTP validation.
     *
     * @param \Illuminate\Http\Request $request The data sent in the request.
     * @return \Illuminate\Http\RedirectResponse Redirects the user back with an error message or to the TOTP validation page.
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails.
     */
    public function validateUserData(Request $request) : RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'g-recaptcha-response' => 'required|captcha'
        ],
            [
                'g-recaptcha-response.required' => 'Captcha is required',
                'g-recaptcha-response.captcha' => 'Captcha is invalid',
            ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        if ($user->email_verified == false) {
            return back()->withErrors([
                'email' => 'Please verify your email address first.',
            ])->onlyInput('email');
        }

        // If Google 2FA is not set up, trigger the setup
        if ($user->google2fa_secret == null) {
            return $this->googleAuthenticatorSetup($user->email);
        }

        // Store email in session and redirect to TOTP validation
        session([
            'email' => $request->email,
            'internal_redirect' => true,
        ]);

        return redirect()->route('totp.validation');
    }

    /**
     * Authenticate the user by validating the TOTP and password.
     *
     * This method verifies the TOTP entered by the user and attempts to authenticate them
     * with their email and password. If the credentials are valid, it regenerates the session
     * and redirects the user to the dashboard.
     *
     * @param \Illuminate\Http\Request $request The request containing the TOTP and password.
     * @return \Illuminate\Http\RedirectResponse Redirects the user to the dashboard or back with errors.
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails.
     */
    public function authenticate(Request $request) : RedirectResponse
    {
        $email = session('email');
        if (!$email) {
            return redirect()->route('auth.signin')->with('error_message', 'You took too much time, please sign in again.');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            session(['internal_redirect' => true]);
            return back()->withErrors([
                'error' => 'The provided credentials do not match our records.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'totp' => 'required|numeric|digits:6',
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha'
        ],
            [
                'g-recaptcha-response.required' => 'Captcha is required',
                'g-recaptcha-response.captcha' => 'Captcha is invalid',
            ]);

        if ($validator->fails()) {
            session(['internal_redirect' => true]);
            return back()->withErrors($validator->errors());
        }

        // Verify the TOTP with Google 2FA
        $google2fa = new Google2FA();
        $isValid = $google2fa->verifyKey(Crypt::decryptString($user->google2fa_secret), $request->totp);

        // Attempt authentication if TOTP is valid
        if ($isValid && Auth::attempt(['email' => $email, 'password' => $request->password])) {
            $request->session()->regenerate();
            session()->forget('email');
            return redirect()->route('dashboard');
        }

        // Redirect back with an error if authentication fails
        session(['internal_redirect' => true]);
        return back()->withErrors([
            'error' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Set up Google 2FA for the user.
     *
     * This method generates a secret key for Google 2FA and provides a QR code URL
     * to link the user's account with the Google Authenticator app.
     * The user is redirected to the 2FA enable page with the QR code.
     *
     * @param string $user_email The email of the user setting up 2FA.
     * @return \Illuminate\Http\RedirectResponse Redirects the user to the enable 2FA page.
     */
    public function googleAuthenticatorSetup(String $user_email) : RedirectResponse
    {
        // Generate Google 2FA secret key
        $google2fa = new Google2FA();
        $secretKey = $google2fa->generateSecretKey();

        // Get the QR code URL for Google Authenticator
        $QRImageUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user_email,
            $secretKey
        );

        // Store the secret key in session and redirect to the 2FA enable page
        session([
            'email' => $user_email,
            'internal_redirect' => true,
            'google2fa_secret' => Crypt::encryptString($secretKey),
        ]);
        return redirect()->route('enable.2fa')->with(compact('QRImageUrl', 'secretKey'));
    }

    /**
     * Link the Google Authenticator to the user's account.
     *
     * This method verifies the TOTP code entered by the user and links the Google 2FA
     * secret to the user's account in the database. After successful linking, the user
     * is redirected to the sign-in page with a success message.
     *
     * @param \Illuminate\Http\Request $request The request containing the TOTP code.
     * @return \Illuminate\Http\RedirectResponse Redirects the user to the sign-in page with a success or error message.
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails.
     */
    public function linkGoogleAuthenticator(Request $request) : RedirectResponse
    {
        $email = session('email');
        $google2fa_secret = Crypt::decryptString(session('google2fa_secret'));

        // Ensure the session contains email and Google 2FA secret
        if (!$email || !$google2fa_secret) {
            return redirect()->route('auth.signin')->with('error_message', 'You took too much time, please sign in again.');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('auth.signin')->with('error_message', 'User Not Found');
        }

        $validator = Validator::make($request->all(), [
            'totp' => 'required|numeric|digits:6',
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha'
        ],
            [
                'g-recaptcha-response.required' => 'Captcha is required',
                'g-recaptcha-response.captcha' => 'Captcha is invalid',
            ]);

        if ($validator->fails()) {
            session(['internal_redirect' => true]);
            return back()->withErrors($validator->errors());
        }

        // Verify the TOTP code with Google 2FA
        $google2fa = new Google2FA();
        $isValid = $google2fa->verifyKey($google2fa_secret, $request->totp);

        // If TOTP is invalid, return with an error
        if (!$isValid) {
            return back()->withErrors([
                'error' => 'Invalid credentials. Please try again.',
            ]);
        }

        // Validate user credentials
        if (!Auth::validate(['email' => $email, 'password' => $request->password])) {
            return back()->withErrors([
                'error' => 'Invalid credentials. Please try again.',
            ]);
        }

        // Link Google 2FA secret to the user's account
        $user->google2fa_secret = Crypt::encryptString($google2fa_secret);
        $user->save();

        // Clear the session data and redirect to sign-in page with success message
        session()->forget('email');
        session()->forget('google2fa_secret');
        return redirect()->route('auth.signin')->with('message', 'Google Authenticator has been successfully linked.');
    }

    /**
     * Log out the user and invalidate the session.
     *
     * This method invalidates the current session, regenerates the CSRF token,
     * and logs out the user, redirecting them to the sign-in page.
     *
     * @param \Illuminate\Http\Request $request The request instance.
     * @return \Illuminate\Http\RedirectResponse Redirects the user to the sign-in page.
     */
    public function logout(Request $request) : RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::logout();

        return redirect()->route('auth.signin');
    }
}

