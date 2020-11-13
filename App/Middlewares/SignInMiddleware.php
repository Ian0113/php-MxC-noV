<?php
namespace App\Middlewares;

use Core\Base\Middleware;
use Core\Exceptions\MiddlewareException;

class SignInMiddleware extends Middleware
{
    /**
     * 驗證 self::getResponse()->getData()
     * 
     * @var array
     */
    public array $data = [
        'userId'  => null,
        'passWd'  => null,
    ];


    /**
     * 驗證執行區
     */
    public function run()
    {
        parent::run();

        if (!$this->isPasswdVerified()) {
            throw new MiddlewareException("password 有問題", 403);
        }

        if (!$this->isUseridVerified()) {
            throw new MiddlewareException("userid 有問題", 403);
        }
    }

    public function isPasswdVerified()
    {
        // 任意字元 12個以上
        $passwd = self::getRequest()->getData()['passWd'];
        return preg_match("/^((.){12,}+)$/", $passwd);
    }

    public function isUseridVerified()
    {
        // 英數 減號 點號 9個以上 20以下
        $userid = self::getRequest()->getData()['userId'];
        return preg_match("/^([\w\-\.]{9,20}+)$/", $userid);
    }

}