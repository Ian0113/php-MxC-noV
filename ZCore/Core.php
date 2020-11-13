<?php
namespace Core;

use Core\Base\Route;
use Core\Base\BaseIOObj;
use Core\Base\Action;
use Core\Base\Handler;
use Core\Exceptions\CoreException;
use Core\Exceptions\ControllerException;

/**
 * 核心物件
 */
class Core extends BaseIOObj
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 由index.php進入這執行已定義動作
     */
    public function run()
    {
        // 找路由列表
        $direct = $this->routing();
        if (!isset($direct)) {
            throw new CoreException("找不到對應URI 需要在 /config/route.php 內加入 URI 及對應 Controller", 404);
        }

        // 透過 Middleware 驗證 Request
        $middlewares = array_merge(['App\Middlewares\DefaultMiddleware'], $direct['mlist']);
        foreach ($middlewares as $middleware) {
            // 將 Middleware 路徑補齊
            $cp_middleware = $middleware;
            if (!class_exists($cp_middleware)) {
                throw new CoreException("找不到 Middleware 需要新增對應 Middleware '$cp_middleware'", 500);
            } elseif (!method_exists($cp_middleware, 'run')) {
                throw new CoreException("找不到 Function 需要在對應 $middleware 內加入對應 Function 'run'", 500);
            }

            // 執行 Middleware
            (new Action(
                $cp_middleware,
                'run',
            ))->exec();
        }

        // 將路徑補齊
        $controller = $direct['cname'];
        if (!class_exists($controller)) {
            throw new CoreException("找不到 Controller 需要新增對應 Controller '$controller'", 500);
        } elseif (!method_exists($controller, $direct['fname'])) {
            throw new CoreException("找不到 Function 需要在對應 $controller 內加入對應 Function '$direct[fname]'", 500);
        }

        // 執行 Controller
        (new Action(
                $controller,
                $direct['fname'],
                array_merge($direct['args'], ['data' => self::getRequest()->getData()]),
        ))->exec();
    }


    /**
     * 依路由找出 Controller
     * 
     * @return array
     */
    private function routing()
    {
        $list = Route::getList(self::getRequest()->getMethod());                // 取得route list

        $chk = strpos(self::getRequest()->getUri(), '?');                       // 尋找網址後面有無問號
        $reqUri = $chk === false ? self::getRequest()->getUri()
                 : substr(self::getRequest()->getUri(), 0, $chk);               // 將問號及後面移除
        $reqUri = trim($reqUri, '/');                                           // 清除頭尾斜線
        // var_dump($reqUri);

        $spReqUri = explode('/', $reqUri);
        // var_dump($spReqUri);

        foreach ($list as $uri => $action) {
            $uri = trim($uri, '/');                                             // 清除頭尾斜線
            $spUri = explode('/', $uri);                                        // 將uri用斜線切開

            if (count($spReqUri) != count($spUri)) {
                // 路徑不等
                continue;
            }

            $chkParam = preg_match_all('#(?:\{)(.*?)(?:\})#u', $uri, $uriArg);  // 路徑中是否有變數
            if ($reqUri == $uri && !$chkParam) {
                // 沒變數且有從list內找到
                $args = [];
                $exec = explode('@', $action['controller']);                    // 將action切成class跟function
                $controller = $exec[0];
                $function = $exec[1];
                return [
                    'cname'    => $controller,
                    'fname'      => $function,
                    'args'          => $args,
                    'mlist'         => $action['middlewares'],
                ];
            }


            $uriArg = array_values($uriArg[1]);                                 // 從指定uri找出變數"名稱"
            $filter = preg_replace('#(?:\{)(.*?)(?:\})#u', "(.*?)", $uri);      // 將變數位置作為正規表示式
            if (preg_match_all('#^' . $filter . '$#i', $reqUri, $reqUriArg)) {
                // 從request uri找出變數"數值"
                unset($reqUriArg[0]);                                           // 去除多餘
                $reqUriArg = array_values($reqUriArg);                          // unset後將array往前推

                $args = [];
                for ($i=0; $i < count($uriArg); $i++) {
                    // 將找出變數"名稱"及"數值"做對應
                    $args = array_merge($args, [$uriArg[$i] => $reqUriArg[$i][0]]);
                }

                $exec = explode('@', $action['controller']);                    // 將action切成class跟function
                $controller = $exec[0];
                $function = $exec[1];
                return [
                    'cname'         => $controller,
                    'fname'         => $function,
                    'args'          => $args,
                    'mlist'         => $action['middlewares'],
                ];
            }
        }
    }
}

