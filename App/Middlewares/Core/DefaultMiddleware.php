<?php
namespace App\Middlewares;

use Core\Base\Route;
use Core\Base\Middleware;
use Core\Exceptions\MiddlewareException;


/**
 * Core 必定會執行 此middleware (勿刪除)
 */
class DefaultMiddleware extends Middleware
{
    /**
     * 驗證每分鐘的訪問次數
     * 0 || null 不驗證
     *
     * @var int
     */
    public int $timesPerMin = 60;

    /**
     * 驗證 self::getResponse()->getHeader()
     * 
     * @var array
     */
    public array $header = [
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
        if (!$this->isMethodExist()) {
            throw new MiddlewareException("找不到訪問方法", 405);
        }

        if (!$this->isUriVerified()) {
            throw new MiddlewareException("網址列有非英數", 403);
        }

        parent::run();
    }

    /**
     * 驗證 URI 是否有怪符號
     * 
     * @return bool
     */
    private function isUriVerified(): bool
    {
        return (
            !preg_match('#\'|!|@|%|\^|&|\$|\*|\(|\)|\+|\{|\}|\[|\]#u', self::getRequest()->getUri())
            && preg_match('#([/0-9A-Za-z\x{4e00}-\x{9fa5}]+)#u', self::getRequest()->getUri())
        );
    }

    /**
     * 檢查 Route 內是否有訪問的method
     * 
     * @return bool
     */
    private function isMethodExist(): bool
    {
        return Route::getList(self::getRequest()->getMethod()) != null;
    }

}
