<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Users\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Services\MailService;

class ForgotPasswordController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function showForgetPasswordForm()
    {
        return view('auth.forgotpassword');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function submitForgetPasswordForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'exists:users',
        ]);
        if ($validator->fails()) {
            return back()->withError('Email is not registered');
        }
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
          'email' => $request->email,
          'token' => $token,
          'created_at' => Carbon::now()
        ]);

        $subject = 'Reset password';
        $resetPasswordUrl = route('reset.password.get', $token);
        $body = "<h1>Forget Password Email</h1>
                You can reset password from bellow link:
                <a href='{$resetPasswordUrl}'>Reset Password</a>";

        return MailService::mailer($subject, $body, $request->email);
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.resetpassword', ['token' => $token]);
    }

    $test = [
        1 => 'Emp1',
        2 => 'Emp2'
    ]


    foreach ($test as $id => $name) {
        
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_reset_tokens')
                            ->where([
                              'token' => $request->token
                            ])
                            ->first();

        if(!$updatePassword) {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $user = User::where('email', $updatePassword->email)
                    ->update(['password' => bcrypt($request->password)]);

        DB::table('password_reset_tokens')->where(['email'=> $updatePassword->email])->delete();

        return redirect('/')->withSuccess('Your password has been changed!');
    }
}
