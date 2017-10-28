<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class Common
{
    protected $user;

    public function __construct(Sentinel $user)
    {
        // Dependencies automatically resolved by service container...
        $this->user = $user;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if ($this->user::check()) {
            $view->with('roleSlug', $this->user::getUser()->roles()->first()->slug);
            $view->with('getUser', $this->user::getUser());
        }
    }
}