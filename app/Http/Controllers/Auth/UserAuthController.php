<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ForgotPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function forgotPassword(Request $request)
    {
        if (User::where( 'email', $request->email)->exists()) {
            $otp = rand(100000, 999999);
            while (ForgotPassword::where('otp', $otp)->exists()) {
                $displayId = rand(100000, 999999);
            }

            $user = new ForgotPassword();
            $user->email = $request->email;
            $user->otp = $otp;

            if ($user->save()) {
                // $to = $rq->email;
                // $email_for = 'reset_password';
                // $extra['otp'] = $otp;

                // $genOpe = new genOpe();
                // $genOpe->sendEmail($to, $email_for, $extra);
                // return view('auth/forgotpasswordOTP', [ 'email' => $request->email ]);
                return redirect()->route('password.form', ['email' => $request->email]);
            }
        } else {
            return redirect()->back()->with('error', 'You don\'t have an account. Please sign up to create an account or contact to admin')->withInput();
        }
    }

    public function passwordForm(Request $request)
    {
        $email = $request->query('email');
        return view('auth.forgotpasswordOTP', compact('email'));
    }

    public function newPassword(Request $request)
    {
        if (ForgotPassword::where('email', $request->email)->where('otp', $request->otp)->latest()->first()) {
            return redirect()->route('newPassword.form', ['email' => $request->email]);
        } else {
            return back()->withInput()->with('error', 'The OTP you entered is incorrect. Please try again.');
        }
    }

    public function setNewpassordForm(Request $request)
    {
        $email = $request->query('email');
        return view('auth.newPassword', compact('email'));
    }

    public function setPassword(Request $request)
    {

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                 return redirect()->route('newPassword.form', ['email' => $request->email])->with('error', 'User not found.')->withInput();
            }

            $newPassword = Hash::make($request->password);
            User::where('email', $request->email)->update(['password' => $newPassword]);

            DB::commit();
            return redirect()->route('passwordSuccess')->with('success', 'Password updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            dd('eeor');
            return redirect()->route('newPassword.form', ['email' => $request->email])
            ->with('error', 'Something went wrong while updating password.')->withInput();
        }
    }

    public function passwordSuccess(Request $request){

        session()->invalidate();
        session()->regenerateToken();

        return response()->view('auth.passwordSuccess')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
       if ($user) {    }
        Auth::logout();

        $request->session()->invalidate();
        return redirect('/login')->with('success', 'You have been logged out successfully!');
    }

}
