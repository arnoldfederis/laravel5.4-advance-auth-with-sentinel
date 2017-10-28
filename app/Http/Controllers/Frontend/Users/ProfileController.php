<?php

namespace App\Http\Controllers\Frontend\Users;

use App\Models\Account\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * ProfileController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'show']);
    }

    public function show($username)
    {
        $user = User::whereUsername($username)->firstOrFail();

        return view('frontend.users.profile.index', compact('user'));
    }
}
