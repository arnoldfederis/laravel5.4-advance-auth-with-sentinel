<?php

/**
 * Auth Repository
 *
 * @author Arnolfo Federis <dev.arnoldfederis@gmail.com>
 * @version v1
 */

namespace App\Repositories;

use App\Models\Account\User;
use App\Notifications\User\UserActivation;
use App\Notifications\User\UserResetPassword;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

class AuthRepository
{
    /**
     * Start of Authentication
     *
     * Authenticate user
     *
     * @param  \Illuminate\Http\Request $request
     * @return Cartalyst\Sentinel\Laravel\Facades\Sentinel::authenticate()
     */
    public static function auth($request)
    {
        validator($request->all(), [
            'login'     =>  'required|string',
            'password'  =>  'required|string'
        ], ['login.required' => trans('auth.required_login')])->validate();

        isset($request->remember) ? $remember = true : $remember = false;

        return Sentinel::authenticate($request->all(), $remember) ? redirect()->intended('/') : static::failed($request);
    }

    /**
     * Redirect if authentication failed
     *
     * @param  \Illuminate\Http\Request $request
     * @return redirect()
     */
    public static function failed($request)
    {
        return redirect()->back()->withInput($request->only('login'))
            ->withErrors(['login' => trans('auth.failed')]);
    }

    /**
     * Redirect if the account is not activated.
     *
     * @param  \Illuminate\Http\Request $request
     * @return redirect()
     */
    public static function notActivated($request)
    {
        return redirect()->back()->withInput($request->only('login'))
            ->withErrors(['login' => trans('auth.activation')]);
    }

    /**
     * Redirect if user has many login attempt.
     *
     * @param Cartalyst\Sentinel\Checkpoints\ThrottlingException $e, \Illuminate\Http\Request $request
     * @return redirect()
     */
    public static function throttle($e, $request)
    {
        return redirect()->back()->withInput($request->only('login'))
            ->withErrors([
                'throttle' => trans('auth.throttle', ['seconds' => $e->getDelay()])
            ]);
    }

    /**
     * End of Authentication
     *
     * User Logout
     *
     * @return redirect()
     */
    public static function logout()
    {
        Sentinel::logout();
        return redirect('/');
    }

    /**
     * Start of Registration
     *
     * Register User and Store as normal user
     *
     * @param  \Illuminate\Http\Request $request
     * @return redirect()
     */
    public static function store($request)
    {
        $credentials = [
            'first_name' =>  $request->first_name,
            'last_name'  =>  $request->last_name,
            'email'      =>  $request->email,
            'username'   =>  str_slug($request->username),
            'password'   =>  $request->password
        ];

        validator($request->all(), [
            'first_name'     =>  'required|min:5',
            'last_name'      =>  'required|min:5',
            'email'          =>  'required|unique:users',
            'username'       =>  'required|min:5|unique:users',
            'password'       =>  'required|min:5|confirmed'
        ])->validate();

        $user = Sentinel::register($credentials);

        $user->permissions = [
            'normal_user' => true
        ];

        $user->save();

        $activation = Activation::create($user);

        static::sendActivation($request->email, $activation->code);

        $role = Sentinel::findRoleBySlug('normal_user');

        $role->users()->attach($user);

        return redirect('login')->with(['status' => trans('auth.send_email')]);
    }

    /**
     * Send Activation Link
     *
     * @param  $email, $code
     * @return $user->notify()
     */
    protected static function sendActivation($email, $code)
    {
        $user = User::whereEmail($email)->first();
        return $user->notify(new UserActivation($user, $code));
    }

    /**
     * User Activation
     *
     * @param  $email, $code
     * @return Cartalyst\Sentinel\Laravel\Facades\Activation::complete() else re-activation failed
     */
    public static function activate($email, $code)
    {
        $user = User::whereEmail($email)->first();
        return Activation::complete($user, $code) ? static::activated() : static::reActivated();
    }

    /**
     * User Activation Redirect
     *
     * @return redirect()
     */
    public static function activated()
    {
        return redirect('login')->with(['status' => trans('auth.activated')]);
    }

    /**
     * End of Registration
     *
     * User Activation Redirect if the user already activate their account
     *
     * @return redirect()
     */
    public static function reActivated()
    {
        return redirect('login')->withErrors(['login' => trans('auth.re_activated')]);
    }

    /**
     * Start of Password Reset
     *
     * Forgot Password request to reset password
     *
     * @param  \Illuminate\Http\Request $request
     * @return redirect()
     */
    public static function request($request)
    {
        validator($request->all(), [
            'login' =>  'required'
        ])->validate();

        $user = User::whereEmail($request->login)->orWhere('username', $request->login)->first();

        if (count($user) == 0) {
            return redirect()->back()->withInput($request->only('login'))
                ->withErrors(['user_null' => trans('passwords.user')]);
        }

        $reset = Reminder::exists($user) ?: Reminder::create($user);

        static::sendReset($user, $reset->code);

        return redirect()->back()->with(['status' => trans('passwords.sent')]);
    }

    /**
     * Send Password Reset Link
     *
     * @param  $user, $code
     * @return $user->notify()
     */
    protected static function sendReset($user, $code)
    {
        return $user->notify(new UserResetPassword($user, $code));
    }

    /**
     * Frontend Reset Password View
     *
     * @param  $email, $code
     * @return view() if success otherwise as redirect as error
     */
    public static function reset($email, $code)
    {
        $user = User::whereEmail($email)->first();

        if (count($user) == 0) {
            return static::tokenInvalid();
        }

        if ($reset = Reminder::exists($user)) {
            if ($code == $reset->code) {
                return view('auth.passwords.reset', [
                    'email' => $email,
                    'code'  => $code
                ]);
            }
            return static::tokenInvalid();
        }
        return static::tokenInvalid();
    }

    /**
     * Change the User password and Redirect as authenticated
     *
     * @param  \Illuminate\Http\Request $request, $email, $code
     * @return redirect() if success otherwise redirect as error
     */
    public static function change($request, $email, $code)
    {
        validator($request->all(), [
            'password'  =>  'confirmed|required|min:5',
            'password_confirmation' =>  'required|min:5'
        ])->validate();

        $user = User::whereEmail($email)->first();

        if (count($user) == 0) {
            return static::resetRedirect($email);
        }

        if ($reset = Reminder::exists($user)) {
            if ($code == $reset->code) {
                Reminder::complete($user, $code, $request->password);
                return redirect('login')->with(['status' => trans('passwords.success')]);
            }
            return static::resetRedirect($email);
        }
        return static::resetRedirect($email);
    }

    /**
     * Reset Redirect Error
     *
     * @return redirect() as reset error
     */
    public static function resetRedirect($email)
    {
        return redirect()->back()->withInput(['email' => $email])->withErrors(['token_invalid' => trans('passwords.token')]);
    }

    /**
     * End of Password Reset
     *
     * @return redirect() as token invalid error
     */
    public static function tokenInvalid()
    {
        return redirect('/login')->with(['token_invalid' => trans('passwords.token')]);
    }
}