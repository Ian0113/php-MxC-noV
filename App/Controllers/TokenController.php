<?php
namespace App\Controllers;

use Auth\Auth;
use Core\Base\Controller;

class TokenController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get()
    {
        $token = Auth::token();
        if ($token == null) {
            $token = md5(uniqid(rand(1000, 9999).rand(10000, 99999)));
            Auth::setToken($token);
        }
        self::getResponse()->setData('token', $token);
        self::getResponse()->setHeader('token: '.$token);
    }
}