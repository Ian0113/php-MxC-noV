<?php
namespace App\Middlewares;

use Auth\Auth;
use Core\Base\Middleware;
use Core\Exceptions\MiddlewareException;

class TokenMiddleware extends Middleware
{
    /**
     * 驗證 self::getResponse()->getHeader()
     * 
     * @var array
     */
    public array $header = [
        'token' => null
    ];


    /**
     * 驗證 self::getResponse()->getData()
     * 
     * @var array
     */
    public array $data = [
    ];

    /**
     * 驗證執行區
     */
    public function run()
    {
        parent::run();

        // if (Auth::token() != self::getRequest()->getHeader()['token']) {
        //     throw new MiddlewareException("token 有誤", 403);
        // }

        // 驗證完更換 token
        $token = md5(uniqid(rand(1000, 9999).rand(10000, 99999)));
        Auth::setToken($token);
        self::getResponse()->setHeader('token: '.$token);
    }

}
