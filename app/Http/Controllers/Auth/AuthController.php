<?php

/**
 * Auth Controller
 *
 * @author Arnolfo Federis <dev.arnoldfederis@gmail.com>
 * @version v1
 */

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AuthRepository;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;

class AuthController extends Controller
{
    protected $request;
    protected $auth;

    /**
     * Create a new controller instance.
     *
     * @param Request $request
     * @param AuthRepository $auth
     */
    public function __construct(Request $request, AuthRepository $auth)
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->request = $request;
        $this->auth = $auth;
    }

    /**
     * Start of Authentication
     *
     * Frontend Login View
     *
     * @return view()
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate user
     *
     * @param  \Illuminate\Http\Request $request
     * @return App\Repositories\AuthRepository::auth()
     * @return App\Repositories\AuthRepository::notActivated()
     * @return App\Repositories\AuthRepository::throttle()
     */
    public function auth()
    {
        try {
            return $this->auth::auth($this->request);
        } catch (NotActivatedException $e) {
            return $this->auth::notActivated($this->request);
        } catch (ThrottlingException $e) {
            return $this->auth::throttle($e, $this->request);
        }
    }

    /**
     * End of Authentication
     *
     * Frontend Logout
     *
     * @return App\Repositories\AuthRepository::logout()
     */
    public function logout()
    {
        return $this->auth::logout();
    }

    /**
     * Start of Registration
     *
     * Frontend Register View
     *
     * @return view()
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Register User and Store as normal user
     *
     * @param  \Illuminate\Http\Request $request
     * @return App\Repositories\AuthRepository::store()
     */
    public function store()
    {
        return $this->auth::store($this->request);
    }

    /**
     * End of Registration
     *
     * User Activation
     *
     * @param  $email, $code
     * @return App\Repositories\AuthRepository::activate()
     */
    public function activate($email, $code)
    {
        return $this->auth::activate($email, $code);
    }

    /**
     * Start of Password Reset
     *
     * Frontend Forgot Password View
     *
     * @return view()
     */
    public function forgot()
    {
        return view('auth.passwords.forgot');
    }

    /**
     * Forgot Password request to reset password
     *
     * @param  \Illuminate\Http\Request $request
     * @return App\Repositories\AuthRepository::request()
     */
    public function request()
    {
        return $this->auth::request($this->request);
    }

    /**
     * Frontend Reset Password View
     *
     * @param  $email, $code
     * @return App\Repositories\AuthRepository::reset()
     */
    public function reset($email, $code)
    {
        return $this->auth::reset($email, $code);
    }

    /**
     * End of Password Reset
     *
     * Change the password of the User
     *
     * @param  \Illuminate\Http\Request $request, $email, $code
     * @return App\Repositories\AuthRepository::change()
     */
    public function change($email, $code)
    {
        return $this->auth::change($this->request, $email, $code);
    }
}
