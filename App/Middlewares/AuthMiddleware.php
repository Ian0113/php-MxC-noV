<?php
namespace App\Middlewares;

use Auth\Auth;
use Core\Base\Middleware;
use Core\Exceptions\MiddlewareException;

class AuthMiddleware extends Middleware
{

    /**
     * 驗證執行區
     */
    public function run()
    {
        parent::run();

        if (Auth::user() == null) {
            throw new MiddlewareException("請登入", 403);
        }
    }

}
