<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use DesignMyNight\Mongodb\Auth\User as MonogoAuth;
class User extends MonogoAuth
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $connection = 'mongodb';
    protected $collection = 'User';
    protected $fillable = [
        'name', 'email', 'password', 'is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $casts = [
        'is_admin' => 'boolean',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function isAdmin()
    {
        return $this->is_admin; // this looks for an admin column in your users table
    }
    public function get_userid()
    {
        return $this->_id; // this looks for an admin column in your users table
    }
    public function update($id=NULL, $name=NULL, $email=NULL)
    {
        $data = array(
            'name' => $name,
            'email' => $email
        );
        User::where('_id', $id)->update($data);
    }
}
