<?php
namespace Auth;

use Core\Base\Session;

class Auth
{
    private const SESSION_KEY = '{Auth}';

    public static function setToken($token)
    {
        Session::register(self::SESSION_KEY);
        if (!Session::set(self::SESSION_KEY, 'token', $token)) {
            Session::update(self::SESSION_KEY, 'token', $token);
        }
    }

    public static function token()
    {
        $token = null;
        Session::get(self::SESSION_KEY, 'token', $token);
        return $token;
    }

    public static function userSignInUp($user)
    {
        Session::register(self::SESSION_KEY);
        if (!Session::set(self::SESSION_KEY, 'user', $user)) {
            Session::update(self::SESSION_KEY, 'user', $user);
        }
    }

    public static function userSignOut()
    {
        Session::delete(self::SESSION_KEY, 'user');
    }

    public static function user()
    {
        Session::get(self::SESSION_KEY, 'user', $user);
        return $user;
    }

}