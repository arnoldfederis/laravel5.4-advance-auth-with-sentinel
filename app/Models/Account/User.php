<?php

namespace App\Models\Account;

use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Cartalyst\Sentinel\Users\EloquentUser;

class User extends EloquentUser
{
    use Notifiable;

    /**
     * @var array
     */
    protected $fillable = [
        'email',
        'username',
        'password',
        'last_name',
        'first_name',
        'permissions',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $appends = ['full_name'];

    protected $loginNames = ['email', 'username'];

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucfirst($value);
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucfirst($value);
    }

    public function roles()
    {
        return $this->belongsToMany(EloquentRole::class, 'role_users', 'user_id', 'role_id')->withTimeStamps();
    }

    public function getFullNameAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }
}
